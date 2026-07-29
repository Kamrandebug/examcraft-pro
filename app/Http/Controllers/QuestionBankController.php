<?php

namespace App\Http\Controllers;

use App\Models\QuestionBank;
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
        ]);

        $data = $request->only(['question_text']);
        // Temporarily fallback to first user if not authenticated since auth is disabled
        $data['user_id'] = Auth::id() ?? (\App\Models\User::first()->id ?? 1);

        if ($request->hasFile('question_image')) {
            $path = $request->file('question_image')->store('questions', 'public');
            $data['question_image'] = $path;
        }

        QuestionBank::create($data);

        return redirect()->route('admin.questions.index')
                         ->with('success', 'Question added successfully.');
    }

    /**
     * Display the specified question with its options.
     */
    public function show(QuestionBank $questionBank)
    {
        $questionBank->load('options');

        return view('question_bank.show', compact('questionBank'));
    }

    /**
     * Show the form for editing the specified question.
     */
    public function edit(QuestionBank $questionBank)
    {
        return view('question_bank.edit', compact('questionBank'));
    }

    /**
     * Update the specified question in the database.
     */
    public function update(Request $request, QuestionBank $questionBank)
    {
        $validated = $request->validate([
            'source_paper_code' => 'nullable|string|max:50',
            'session'           => 'nullable|string|max:50',
            'year'              => 'nullable|digits:4',
            'subject'           => 'nullable|string|max:255',
            'topic'             => 'nullable|string|max:255',
            'difficulty'        => 'nullable|string|in:easy,medium,hard',
            'stem'              => 'required|string',
            'stem_image_url'    => 'nullable|url',
            'option_type'       => 'nullable|string|max:50',
            'correct_answer'    => 'nullable|string|max:10',
            'marks'             => 'nullable|integer|min:0',
            'metadata'          => 'nullable|array',
        ]);

        $questionBank->update($validated);

        return redirect()->route('question-bank.show', $questionBank)
                         ->with('success', 'Question updated successfully.');
    }

    /**
     * Remove the specified question from the bank.
     */
    public function destroy(QuestionBank $questionBank)
    {
        $questionBank->delete();

        return redirect()->route('admin.questions.index')
                         ->with('success', 'Question deleted successfully.');
    }
}
