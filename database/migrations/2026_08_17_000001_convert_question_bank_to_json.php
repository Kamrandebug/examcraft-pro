<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Add the `data` JSON column after `grade`.
        Schema::table('question_bank', function (Blueprint $table) {
            $table->json('data')->nullable()->after('grade');
        });

        // 2. Migrate existing rows: build the JSON structure from the old
        //    relational columns + question_bank_options rows.
        DB::table('question_bank')->orderBy('id')->each(function ($question) {
            $optionRows = DB::table('question_bank_options')
                ->where('question_id', $question->id)
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get();

            $data = [
                'stem_text'      => $question->question_text ?? null,
                'stem_image'     => null,
                'options'        => $this->buildOptions($optionRows),
                'correct_answer' => null,
            ];

            DB::table('question_bank')
                ->where('id', $question->id)
                ->update(['data' => json_encode($data)]);
        });

        // 3. Drop the now-redundant options table.
        Schema::dropIfExists('question_bank_options');

        // 4. Drop the `question_text` column (its content now lives in JSON).
        Schema::table('question_bank', function (Blueprint $table) {
            $table->dropColumn('question_text');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1. Add back the `question_text` column.
        Schema::table('question_bank', function (Blueprint $table) {
            $table->text('question_text')->nullable()->after('grade');
        });

        // 2. Add back the options table.
        Schema::create('question_bank_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_bank_id')->constrained('question_bank')->cascadeOnDelete();
            $table->string('label')->nullable();
            $table->text('option_text')->nullable();
            $table->timestamps();
        });

        // 3. Remove the `data` column.
        Schema::table('question_bank', function (Blueprint $table) {
            $table->dropColumn('data');
        });
    }

    /**
     * Map question_bank_options rows into the JSON options array.
     *
     * Handles both historical layouts:
     *  - row-per-option  (label + option_text + option_image_url)
     *  - wide-row layout (option_a_text .. option_f_image)
     */
    private function buildOptions($rows): array
    {
        if ($rows->isEmpty()) {
            return [];
        }

        $options = [];
        $first = (array) $rows->first();

        // Wide-row layout: a single row carrying option_a_text .. option_f_text.
        if ($this->hasWideColumns($first)) {
            foreach (['A', 'B', 'C', 'D', 'E', 'F'] as $label) {
                $lower = strtolower($label);
                $text  = $first["option_{$lower}_text"] ?? null;
                $image = $first["option_{$lower}_image"] ?? null;

                if ($text !== null || $image !== null) {
                    $options[] = [
                        'label' => $label,
                        'text'  => $text,
                        'image' => $image,
                    ];
                }
            }
            return $options;
        }

        // Row-per-option layout.
        foreach ($rows as $row) {
            $options[] = [
                'label' => $row->label ?? null,
                'text'  => $row->option_text ?? null,
                'image' => $row->option_image_url ?? null,
            ];
        }

        return $options;
    }

    private function hasWideColumns(array $row): bool
    {
        foreach (['a', 'b', 'c', 'd'] as $lower) {
            if (! empty($row["option_{$lower}_text"] ?? null) || ! empty($row["option_{$lower}_image"] ?? null)) {
                return true;
            }
        }
        return false;
    }
};
