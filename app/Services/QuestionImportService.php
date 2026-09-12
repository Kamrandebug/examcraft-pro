<?php

namespace App\Services;

use App\Models\QuestionBank;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\IOFactory;
use SplFileObject;

class QuestionImportService
{
    private const MIN_OPTIONS = 4;
    private const MAX_OPTIONS = 10;
    private const OPTION_LABELS = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J'];

    /**
     * Parse and import questions from uploaded file
     */
    public function import($file, string $grade, string $subject): array
    {
        $extension = strtolower($file->getClientOriginalExtension());

        if ($extension === 'csv') {
            $rows = $this->parseCSV($file);
        } elseif (in_array($extension, ['xlsx', 'xls'])) {
            $rows = $this->parseExcel($file);
        } else {
            return [
                'success' => false,
                'error' => 'Unsupported file type. Please use CSV or Excel.',
                'imported' => 0,
                'failed' => 0,
                'errors' => [],
            ];
        }

        return $this->validateAndImport($rows, $grade, $subject);
    }

    /**
     * Parse CSV file
     */
    private function parseCSV($file): array
    {
        $rows = [];
        $filePath = $file->getRealPath();

        $csv = new SplFileObject($filePath);
        $csv->setFlags(SplFileObject::READ_CSV | SplFileObject::SKIP_EMPTY);

        $headerRow = null;
        $rowNum = 0;

        foreach ($csv as $row) {
            $rowNum++;

            // Skip empty rows
            if (!$row || implode('', (array)$row) === '') {
                continue;
            }

            // First row is header
            if ($headerRow === null) {
                $headerRow = $row;
                continue;
            }

            $rows[] = [
                'line' => $rowNum,
                'data' => $this->mapRowToData((array)$row, (array)$headerRow),
            ];
        }

        return $rows;
    }

    /**
     * Parse Excel file
     */
    private function parseExcel($file): array
    {
        $rows = [];

        try {
            $spreadsheet = IOFactory::load($file->getRealPath());
            $sheet = $spreadsheet->getActiveSheet();
            $headerRow = null;
            $rowNum = 0;

            foreach ($sheet->getRowIterator() as $row) {
                $rowNum++;
                $cellIterator = $row->getCellIterator();
                $rowData = [];

                foreach ($cellIterator as $cell) {
                    $rowData[] = $cell->getValue();
                }

                // Skip empty rows
                if (!$rowData || implode('', $rowData) === '') {
                    continue;
                }

                // First row is header
                if ($headerRow === null) {
                    $headerRow = $rowData;
                    continue;
                }

                $rows[] = [
                    'line' => $rowNum,
                    'data' => $this->mapRowToData($rowData, $headerRow),
                ];
            }
        } catch (\Exception $e) {
            return [
                [
                    'line' => 0,
                    'error' => 'Failed to parse Excel file: ' . $e->getMessage(),
                ],
            ];
        }

        return $rows;
    }

    /**
     * Map row data to structured format using headers
     */
    private function mapRowToData(array $row, array $headers): array
    {
        $mapped = [];

        foreach ($headers as $index => $header) {
            $header = trim((string)$header);
            $value = $row[$index] ?? '';
            $mapped[$header] = trim((string)$value);
        }

        return $mapped;
    }

    /**
     * Validate and import questions
     */
    private function validateAndImport(array $rows, string $grade, string $subject): array
    {
        $valid = [];
        $errors = [];

        foreach ($rows as $rowInfo) {
            if (isset($rowInfo['error'])) {
                $errors[] = [
                    'line' => $rowInfo['line'],
                    'error' => $rowInfo['error'],
                ];
                continue;
            }

            $rowData = $rowInfo['data'] ?? [];

            // Skip if row data is empty
            if (empty($rowData)) {
                $errors[] = [
                    'line' => $rowInfo['line'] ?? 'Unknown',
                    'error' => 'Empty row data',
                ];
                continue;
            }

            $validation = $this->validateRow($rowData, $rowInfo['line']);

            if ($validation['valid']) {
                $valid[] = $validation['question'];
            } else {
                $errors[] = [
                    'line' => $rowInfo['line'],
                    'error' => $validation['error'],
                ];
            }
        }

        // If there are errors, return without importing
        if (!empty($errors)) {
            return [
                'success' => false,
                'message' => 'Validation failed. Please fix the following errors:',
                'imported' => 0,
                'failed' => count($errors),
                'total' => count($rows),
                'errors' => $errors,
            ];
        }

        // Insert all valid questions
        $imported = 0;
        $insertErrors = [];

        // Use current user or fallback to admin user (id=8)
        $userId = Auth::id() ?? 8;

        foreach ($valid as $idx => $questionData) {
            try {
                QuestionBank::create([
                    'user_id' => $userId,
                    'grade' => $grade,
                    'subject' => $subject,
                    'marks' => 1,
                    'data' => $questionData,
                ]);
                $imported++;
            } catch (\Exception $e) {
                $insertErrors[] = [
                    'line' => 'Row ' . ($idx + 1),
                    'error' => 'Database error: ' . $e->getMessage(),
                ];
            }
        }

        // If there were insert errors, report them
        if (!empty($insertErrors)) {
            return [
                'success' => false,
                'message' => "Imported $imported questions but encountered " . count($insertErrors) . " database errors.",
                'imported' => $imported,
                'failed' => count($insertErrors),
                'total' => count($valid),
                'errors' => $insertErrors,
            ];
        }

        return [
            'success' => true,
            'message' => "Successfully imported $imported questions.",
            'imported' => $imported,
            'failed' => 0,
            'total' => count($valid),
            'errors' => [],
        ];
    }

    /**
     * Validate a single row of question data
     */
    private function validateRow(array $rowData, int $line): array
    {
        $stemText = $rowData['Question Text'] ?? '';
        $correctAnswer = $rowData['Correct Answer'] ?? '';

        // Validate question text
        if (empty($stemText)) {
            return [
                'valid' => false,
                'error' => 'Missing or empty "Question Text"',
            ];
        }

        $stemText = trim($stemText);
        if (strlen($stemText) < 5) {
            return [
                'valid' => false,
                'error' => 'Question text too short (min 5 characters)',
            ];
        }

        if (strlen($stemText) > 1000) {
            return [
                'valid' => false,
                'error' => 'Question text too long (max 1000 characters)',
            ];
        }

        // Extract options
        $options = [];
        for ($i = 0; $i < self::MAX_OPTIONS; $i++) {
            $label = self::OPTION_LABELS[$i];
            $key = "Option $label";
            $optionText = $rowData[$key] ?? '';

            if (!empty($optionText)) {
                $optionText = trim($optionText);

                if (strlen($optionText) > 500) {
                    return [
                        'valid' => false,
                        'error' => "Option $label text too long (max 500 characters)",
                    ];
                }

                $options[] = [
                    'label' => $label,
                    'text' => $optionText,
                    'image' => null,
                ];
            }
        }

        // Validate option count
        $optionCount = count($options);
        if ($optionCount < self::MIN_OPTIONS) {
            return [
                'valid' => false,
                'error' => "Too few options ($optionCount provided, min " . self::MIN_OPTIONS . " required)",
            ];
        }

        if ($optionCount > self::MAX_OPTIONS) {
            return [
                'valid' => false,
                'error' => "Too many options ($optionCount provided, max " . self::MAX_OPTIONS . " allowed)",
            ];
        }

        // Validate correct answer
        if (empty($correctAnswer)) {
            return [
                'valid' => false,
                'error' => 'Missing "Correct Answer"',
            ];
        }

        $correctAnswer = strtoupper(trim($correctAnswer));

        // Check if answer is a valid letter (A-J range based on option count)
        $validAnswers = array_slice(self::OPTION_LABELS, 0, $optionCount);
        if (!in_array($correctAnswer, $validAnswers)) {
            return [
                'valid' => false,
                'error' => "Correct answer '$correctAnswer' not in valid range (A-" . self::OPTION_LABELS[$optionCount - 1] . ")",
            ];
        }

        // Convert letter to index
        $answerIndex = array_search($correctAnswer, self::OPTION_LABELS);

        // Build question data structure
        $questionData = [
            'stem_text' => $stemText,
            'stem_image' => null,
            'options' => $options,
            'correct_answer' => $answerIndex,
        ];

        return [
            'valid' => true,
            'question' => $questionData,
        ];
    }
}
