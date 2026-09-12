<?php

namespace App\Http\Controllers;

use App\Models\QuestionBank;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class QuestionBankController extends Controller
{
    /**
     * Display a listing of question bank entries for the authenticated user.
     */
    public function index()
    {
        $questions = QuestionBank::latest()->get();

        return view('admin.questions.index', compact('questions'));
    }

    /**
     * Show the form for creating a new question.
     */
    public function create()
    {
        return view('admin.questions.create');
    }

    /**
     * Store a newly created question in the database.
     */
    public function store(Request $request)
    {
        // Validate dynamic options (minimum 4, maximum 10)
        $optionCount = count($request->input('option_text', []));

        $request->validate([
            'subject'       => 'required|string|max:100',
            'grade'         => 'required|string|in:O Level,A Level,8th Grade,9th Grade,10th Grade',

            'stem_text'     => 'required_without:stem_image',
            'stem_image'    => 'nullable|image|max:2048',

            'option_text.*' => 'required|string|max:500',
            'option_image.*' => 'nullable|image|max:2048',

            'correct_answer' => 'required|integer|min:0|max:9',
        ]);

        // Validate option count (4-10)
        if ($optionCount < 4 || $optionCount > 10) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['options' => 'You must provide between 4 and 10 options.']);
        }

        $data = $this->buildData($request, null);

        QuestionBank::create([
            'user_id' => Auth::id() ?? (\App\Models\User::first()->id ?? 1),
            'subject' => $request->input('subject'),
            'grade'   => $request->input('grade'),
            'marks'   => 1, // Default marks, removed from form
            'data'    => $data,
        ]);

        return redirect()->route('admin.questions.index')
                         ->with('success', 'Question and options saved successfully.');
    }

    /**
     * Display the specified question with its options.
     */
    public function show(QuestionBank $question)
    {
        return view('admin.questions.show', compact('question'));
    }

    /**
     * Show the form for editing the specified question.
     */
    public function edit(QuestionBank $question)
    {
        return view('admin.questions.edit', compact('question'));
    }

    /**
     * Update the specified question in the database.
     */
    public function update(Request $request, QuestionBank $question)
    {
        // Validate dynamic options (minimum 4, maximum 10)
        $optionCount = count($request->input('option_text', []));

        $request->validate([
            'subject'       => 'required|string|max:100',
            'grade'         => 'required|string|in:O Level,A Level,8th Grade,9th Grade,10th Grade',

            'stem_text'     => 'required_without:stem_image',
            'stem_image'    => 'nullable|image|max:2048',

            'option_text.*' => 'required|string|max:500',
            'option_image.*' => 'nullable|image|max:2048',

            'correct_answer' => 'required|integer|min:0|max:9',
        ]);

        // Validate option count (4-10)
        if ($optionCount < 4 || $optionCount > 10) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['options' => 'You must provide between 4 and 10 options.']);
        }

        $question->update([
            'subject' => $request->input('subject'),
            'grade'   => $request->input('grade'),
            'marks'   => 1, // Default marks, removed from form
            'data'    => $this->buildData($request, $question->data),
        ]);

        return redirect()->route('admin.questions.show', $question)
                         ->with('success', 'Question updated successfully.');
    }

    /**
     * Show the bulk import form for creating multiple questions from file
     */
    public function showBulkImport()
    {
        return view('admin.questions.bulk-import');
    }

    /**
     * Store multiple questions from uploaded file
     */
    public function storeBulkImport(Request $request)
    {
        $request->validate([
            'file'    => 'required|file|mimes:csv,xlsx,xls|max:5120',
            'grade'   => 'required|string|in:O Level,A Level,8th Grade,9th Grade,10th Grade',
            'subject' => 'required|string|max:100',
        ]);

        $service = new \App\Services\QuestionImportService();
        $result = $service->import($request->file('file'), $request->input('grade'), $request->input('subject'));

        if ($result['success']) {
            return redirect()->route('admin.questions.index')
                             ->with('success', $result['message']);
        }

        return redirect()->back()
                         ->withInput()
                         ->with('import_result', $result);
    }

    public function filter(Request $request)
    {
        $grade = $request->query('grade');
        $subject = $request->query('subject');

        if (!$grade || !$subject) {
            return response()->json([
                'success' => false,
                'message' => 'Grade and subject are required.',
            ], 422);
        }

        $questions = QuestionBank::where('grade', $grade)
            ->where('subject', $subject)
            ->get()
            ->map(function ($q) {
                $data = $q->data ?? [];

                $options = array_map(function ($opt) {
                    $image = $opt['image'] ?? null;

                    return [
                        'label' => $opt['label'] ?? null,
                        'text'  => $opt['text'] ?? null,
                        'image' => $image ? Storage::url($image) : null,
                    ];
                }, $data['options'] ?? []);

                $stemImage = $data['stem_image'] ?? null;

                return [
                    'id'             => $q->id,
                    'subject'        => $q->subject,
                    'grade'          => $q->grade,
                    'marks'          => $q->marks,
                    'stem_text'      => $data['stem_text'] ?? null,
                    'stem_image'     => $stemImage ? Storage::url($stemImage) : null,
                    'options'        => $options,
                    'correct_answer' => $data['correct_answer'] ?? null,
                ];
            });

        return response()->json([
            'success' => true,
            'data'    => $questions,
        ]);
    }

    /**
     * Build the `data` JSON array from the request.
     *
     * @param  array|null  $existing  The question's current `data` array (for update).
     */
    private function buildData(Request $request, ?array $existing): array
    {
        $data = [
            'stem_text'      => $request->input('stem_text'),
            'stem_image'     => $existing['stem_image'] ?? null,
            'options'        => $this->buildOptions($request, $existing['options'] ?? []),
            'correct_answer' => $request->input('correct_answer'),
        ];

        // Stem image — replace only if a new file is uploaded.
        if ($request->hasFile('stem_image')) {
            $data['stem_image'] = $request->file('stem_image')->store('questions', 'public');
        }

        return $data;
    }

    /**
     * Build the options array from the request (supports 2-10 options).
     */
    private function buildOptions(Request $request, array $existingOptions): array
    {
        $optionLabels = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J'];
        $optionTexts = $request->input('option_text', []);
        $optionImages = $request->file('option_image', []);

        $options = [];

        foreach ($optionTexts as $index => $text) {
            $label = $optionLabels[$index] ?? chr(65 + $index); // Fallback to ASCII

            $image = $existingOptions[$index]['image'] ?? null;

            // If a new image file is uploaded for this option
            if (isset($optionImages[$index]) && $optionImages[$index]) {
                $image = $optionImages[$index]->store('options', 'public');
            }

            $options[] = [
                'label' => $label,
                'text'  => $text,
                'image' => $image,
            ];
        }

        return $options;
    }
}
