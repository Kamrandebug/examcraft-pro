<?php

namespace App\Http\Controllers;

use App\Models\QuestionBankOption;
use App\Models\QuestionBank;
use Illuminate\Http\Request;

class QuestionBankOptionController extends Controller
{
    /**
     * Display all options for a given question.
     */
    public function index(QuestionBank $questionBank)
    {
        $options = $questionBank->options;

        return view('question_bank_options.index', compact('questionBank', 'options'));
    }

    /**
     * Show the form for creating a new option.
     */
    public function create(QuestionBank $questionBank)
    {
        return view('question_bank_options.create', compact('questionBank'));
    }

    /**
     * Store a newly created option in the database.
     */
    public function store(Request $request, QuestionBank $questionBank)
    {
        $validated = $request->validate([
            'label'            => 'required|string|max:10',
            'option_text'      => 'nullable|string',
            'option_image_url' => 'nullable|url',
            'option_cells'     => 'nullable|array',
            'sort_order'       => 'required|integer|min:0',
        ]);

        $validated['question_id'] = $questionBank->id;

        $option = QuestionBankOption::create($validated);

        return redirect()->route('question-bank.options.index', $questionBank)
                         ->with('success', 'Option added successfully.');
    }

    /**
     * Display the specified option.
     */
    public function show(QuestionBank $questionBank, QuestionBankOption $option)
    {
        return view('question_bank_options.show', compact('questionBank', 'option'));
    }

    /**
     * Show the form for editing the specified option.
     */
    public function edit(QuestionBank $questionBank, QuestionBankOption $option)
    {
        return view('question_bank_options.edit', compact('questionBank', 'option'));
    }

    /**
     * Update the specified option in the database.
     */
    public function update(Request $request, QuestionBank $questionBank, QuestionBankOption $option)
    {
        $validated = $request->validate([
            'label'            => 'required|string|max:10',
            'option_text'      => 'nullable|string',
            'option_image_url' => 'nullable|url',
            'option_cells'     => 'nullable|array',
            'sort_order'       => 'required|integer|min:0',
        ]);

        $option->update($validated);

        return redirect()->route('question-bank.options.index', $questionBank)
                         ->with('success', 'Option updated successfully.');
    }

    /**
     * Remove the specified option.
     */
    public function destroy(QuestionBank $questionBank, QuestionBankOption $option)
    {
        $option->delete();

        return redirect()->route('question-bank.options.index', $questionBank)
                         ->with('success', 'Option deleted successfully.');
    }
}
