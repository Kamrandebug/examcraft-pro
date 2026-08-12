<?php

namespace App\Http\Controllers;

use App\Models\QuestionBank;
use App\Models\QuestionBankOption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuestionBankController extends Controller
{
    /**
     * Display a listing of question bank entries for the authenticated user.
     */
    public function index()
    {
        $questions = QuestionBank::with('options')
                                 ->latest()
                                 ->get();

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
            'question_text' => 'required_without:question_image',
            'question_image' => 'required_without:question_text|image|max:2048',

            'grade'   => 'required|string|in:O Level,A Level,8th Grade,9th Grade,10th Grade',
            'subject' => 'required|string|max:100',

            // Options A-D are mandatory: text or image required for each.
            'options.A.text' => 'required_without:options.A.image',
            'options.A.image' => 'required_without:options.A.text|image|max:2048',
            'options.B.text' => 'required_without:options.B.image',
            'options.B.image' => 'required_without:options.B.text|image|max:2048',
            'options.C.text' => 'required_without:options.C.image',
            'options.C.image' => 'required_without:options.C.text|image|max:2048',
            'options.D.text' => 'required_without:options.D.image',
            'options.D.image' => 'required_without:options.D.text|image|max:2048',

            // Options E/F are optional, but image (if provided) must be valid.
            'options.E.text' => 'nullable',
            'options.E.image' => 'nullable|image|max:2048',
            'options.F.text' => 'nullable',
            'options.F.image' => 'nullable|image|max:2048',

            'correct_option' => 'required|in:A,B,C,D,E,F',
        ]);

        $data = $request->only(['question_text', 'grade', 'subject']);
        // Temporarily fallback to first user if not authenticated since auth is disabled
        $data['user_id'] = Auth::id() ?? (\App\Models\User::first()->id ?? 1);

        if ($request->hasFile('question_image')) {
            $path = $request->file('question_image')->store('questions', 'public');
            $data['question_image'] = $path;
        }

        $question = QuestionBank::create($data);

        // Save options for the question. The question_bank_options table stores
        // one wide row per question (option_a_text .. option_f_image + correct_option).
        $optionData = [
            'question_id'  => $question->id,
            'correct_option' => $request->input('correct_option'),
        ];

        foreach (['A', 'B', 'C', 'D', 'E', 'F'] as $label) {
            $opt = strtolower($label);
            $fieldText = "option_{$opt}_text";
            $fieldImage = "option_{$opt}_image";

            $optionData[$fieldText] = $request->input("options.{$label}.text") ?? null;

            if ($request->hasFile("options.{$label}.image")) {
                $optionData[$fieldImage] = $request->file("options.{$label}.image")->store('options', 'public');
            } else {
                $optionData[$fieldImage] = null;
            }
        }

        QuestionBankOption::updateOrCreate(['question_id' => $question->id], $optionData);

        return redirect()->route('admin.questions.index')
                         ->with('success', 'Question and options saved successfully.');
    }

    /**
     * Display the specified question with its options.
     */
    public function show(QuestionBank $question)
    {
        $question->load('options');

        return view('admin.questions.show', compact('question'));
    }

    /**
     * Show the form for editing the specified question.
     */
    public function edit(QuestionBank $question)
    {
        $question->load('options');

        return view('admin.questions.edit', compact('question'));
    }

    /**
     * Update the specified question in the database.
     */
    public function update(Request $request, QuestionBank $question)
    {
        $validated = $request->validate([
            'source_paper_code' => 'nullable|string|max:50',
            'session'           => 'nullable|string|max:50',
            'year'              => 'nullable|digits:4',
            'grade'             => 'required|string|in:O Level,A Level,8th Grade,9th Grade,10th Grade',
            'subject'           => 'required|string|max:100',
            'topic'             => 'nullable|string|max:255',
            'difficulty'        => 'nullable|string|in:easy,medium,hard',
            'question_text'     => 'required|string',
            'question_image'    => 'nullable|image|max:2048',
            'option_type'       => 'nullable|string|max:50',
            'correct_answer'    => 'nullable|string|max:10',
            'marks'             => 'nullable|integer|min:0',
            'metadata'          => 'nullable|array',
        ]);

        if ($request->hasFile('question_image')) {
            $validated['question_image'] = $request->file('question_image')->store('questions', 'public');
        }

        $question->update($validated);

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

        $questions = QuestionBank::with('options')
            ->where('grade', $grade)
            ->where('subject', $subject)
            ->get()
            ->map(function ($q) {
                $opt = $q->options->first();

                $optionsList = [];
                $letters = ['A', 'B', 'C', 'D'];

                if ($opt) {
                    foreach ($letters as $letter) {
                        $lower = strtolower($letter);
                        $text = $opt->{'option_' . $lower . '_text'} ?? null;
                        if ($text !== null) {
                            $optionsList[] = [
                                'label' => $letter,
                                'option_text' => $text,
                            ];
                        }
                    }
                }

                return [
                    'id'       => $q->id,
                    'question' => $q->question_text,
                    'options'  => $optionsList,
                ];
            });

        return response()->json([
            'success' => true,
            'data'    => $questions,
        ]);
    }
}
