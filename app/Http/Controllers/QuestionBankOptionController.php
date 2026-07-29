<?php

namespace App\Http\Controllers;

use App\Models\QuestionBank;
use App\Models\QuestionBankOption;
use Illuminate\Http\Request;

class QuestionBankOptionController extends Controller
{
    /**
     * Display all questions and their options for management.
     */
    public function index()
    {
        $questions = QuestionBank::with('options')->latest()->get();
        return view('admin.questions.options', compact('questions'));
    }

    /**
     * Store or update options for a specific question.
     */
    public function store(Request $request)
    {
        $questionId = $request->question_id;
        
        $request->validate([
            'question_id' => 'required|exists:question_bank,id',
            'option_a_text' => 'required_without:option_a_image',
            'option_a_image' => 'required_without:option_a_text|image|max:2048',
            'option_b_text' => 'required_without:option_b_image',
            'option_b_image' => 'required_without:option_b_text|image|max:2048',
            'option_c_text' => 'required_without:option_c_image',
            'option_c_image' => 'required_without:option_c_text|image|max:2048',
            'option_d_text' => 'required_without:option_d_image',
            'option_d_image' => 'required_without:option_d_text|image|max:2048',
            
            // Optional E
            'option_e_text' => 'nullable|required_without:option_e_image',
            'option_e_image' => 'nullable|image|max:2048',
            
            // Optional F
            'option_f_text' => 'nullable|required_without:option_f_image',
            'option_f_image' => 'nullable|image|max:2048',
            
            'correct_option' => 'required|in:A,B,C,D,E,F',
        ]);

        $data = $request->only([
            'option_a_text', 'option_b_text', 'option_c_text', 'option_d_text', 
            'option_e_text', 'option_f_text',
            'correct_option'
        ]);
        
        // Handle image uploads
        foreach (['a', 'b', 'c', 'd', 'e', 'f'] as $opt) {
            $fieldName = "option_{$opt}_image";
            if ($request->hasFile($fieldName)) {
                $path = $request->file($fieldName)->store('options', 'public');
                $data[$fieldName] = $path;
            }
        }

        // If an option E or F was removed (passed as empty/null but previously existed), 
        // we should ensure they are cleared if they aren't in the request? 
        // Actually, updateOrCreate will only update what's in $data. 
        // If they are nullable, we should explicitly set them to null if not present in request but that might be tricky.
        // For now, let's assume if they are not in the form (removed from DOM), they should be nullified.
        if (!$request->has('option_e_text') && !$request->hasFile('option_e_image')) {
            $data['option_e_text'] = null;
            $data['option_e_image'] = null;
        }
        if (!$request->has('option_f_text') && !$request->hasFile('option_f_image')) {
            $data['option_f_text'] = null;
            $data['option_f_image'] = null;
        }

        QuestionBankOption::updateOrCreate(
            ['question_id' => $questionId],
            $data
        );

        return redirect()->back()->with('success', 'Options saved successfully.');
    }
}
