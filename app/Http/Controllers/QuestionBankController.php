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
        $questions = QuestionBank::where('user_id', Auth::id())
                                 ->with('options')
                                 ->latest()
                                 ->paginate(20);

        return view('question_bank.index', compact('questions'));
    }

    /**
     * Show the form for creating a new question.
     */
    public function create()
    {
        return view('question_bank.create');
    }

    /**
     * Store a newly created question in the database.
     */
    public function store(Request $request)
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

        $validated['user_id'] = Auth::id();

        $question = QuestionBank::create($validated);

        return redirect()->route('question-bank.show', $question)
                         ->with('success', 'Question added to bank successfully.');
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

        return redirect()->route('question-bank.index')
                         ->with('success', 'Question deleted successfully.');
    }
}
