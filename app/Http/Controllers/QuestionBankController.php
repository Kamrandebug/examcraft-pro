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
        $request->validate([
            'subject'       => 'required|string|max:100',
            'grade'         => 'required|string|in:O Level,A Level,8th Grade,9th Grade,10th Grade',
            'marks'         => 'nullable|integer|min:0',

            'stem_text'     => 'required_without:stem_image',
            'stem_image'    => 'nullable|image|max:2048',

            'option_a_text' => 'required_without:option_a_image',
            'option_a_image' => 'nullable|image|max:2048',
            'option_b_text' => 'required_without:option_b_image',
            'option_b_image' => 'nullable|image|max:2048',
            'option_c_text' => 'required_without:option_c_image',
            'option_c_image' => 'nullable|image|max:2048',
            'option_d_text' => 'required_without:option_d_image',
            'option_d_image' => 'nullable|image|max:2048',

            'correct_answer' => 'required|in:A,B,C,D',
        ]);

        $data = $this->buildData($request, null);

        QuestionBank::create([
            'user_id' => Auth::id() ?? (\App\Models\User::first()->id ?? 1),
            'subject' => $request->input('subject'),
            'grade'   => $request->input('grade'),
            'marks'   => $request->input('marks', 1),
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
        $request->validate([
            'subject'       => 'required|string|max:100',
            'grade'         => 'required|string|in:O Level,A Level,8th Grade,9th Grade,10th Grade',
            'marks'         => 'nullable|integer|min:0',

            'stem_text'     => 'required_without:stem_image',
            'stem_image'    => 'nullable|image|max:2048',

            'option_a_text' => 'required_without:option_a_image',
            'option_a_image' => 'nullable|image|max:2048',
            'option_b_text' => 'required_without:option_b_image',
            'option_b_image' => 'nullable|image|max:2048',
            'option_c_text' => 'required_without:option_c_image',
            'option_c_image' => 'nullable|image|max:2048',
            'option_d_text' => 'required_without:option_d_image',
            'option_d_image' => 'nullable|image|max:2048',

            'correct_answer' => 'required|in:A,B,C,D',
        ]);

        $question->update([
            'subject' => $request->input('subject'),
            'grade'   => $request->input('grade'),
            'marks'   => $request->input('marks', $question->marks ?? 1),
            'data'    => $this->buildData($request, $question->data),
        ]);

        return redirect()->route('admin.questions.show', $question)
                         ->with('success', 'Question updated successfully.');
    }

    /**
     * Remove the specified question from the bank.
     */
    public function destroy(QuestionBank $question)
    {
        $question->delete();

        return redirect()->route('admin.questions.index')
                         ->with('success', 'Question deleted successfully.');
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
     * Build the options array from the request, preserving existing images
     * when no new file is provided for an option.
     */
    private function buildOptions(Request $request, array $existingOptions): array
    {
        $existingByLabel = [];
        foreach ($existingOptions as $opt) {
            $existingByLabel[$opt['label'] ?? ''] = $opt;
        }

        $options = [];
        foreach (['A', 'B', 'C', 'D'] as $label) {
            $lower = strtolower($label);
            $textKey  = "option_{$lower}_text";
            $imageKey = "option_{$lower}_image";

            $image = $existingByLabel[$label]['image'] ?? null;
            if ($request->hasFile($imageKey)) {
                $image = $request->file($imageKey)->store('options', 'public');
            }

            $options[] = [
                'label' => $label,
                'text'  => $request->input($textKey),
                'image' => $image,
            ];
        }

        return $options;
    }
}
