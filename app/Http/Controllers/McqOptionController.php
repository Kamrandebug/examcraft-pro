<?php

namespace App\Http\Controllers;

use App\Models\McqOption;
use App\Models\McqBlock;
use Illuminate\Http\Request;

class McqOptionController extends Controller
{
    /**
     * Display all options for a given MCQ block.
     */
    public function index(McqBlock $mcqBlock)
    {
        $options = $mcqBlock->options;

        return view('mcq_options.index', compact('mcqBlock', 'options'));
    }

    /**
     * Show the form for creating a new option.
     */
    public function create(McqBlock $mcqBlock)
    {
        return view('mcq_options.create', compact('mcqBlock'));
    }

    /**
     * Store a newly created option in the database.
     */
    public function store(Request $request, McqBlock $mcqBlock)
    {
        $validated = $request->validate([
            'label'            => 'required|string|max:10',
            'option_text'      => 'nullable|string',
            'option_image_url' => 'nullable|url',
            'option_cells'     => 'nullable|array',
            'sort_order'       => 'required|integer|min:0',
        ]);

        $validated['mcq_block_id'] = $mcqBlock->id;

        $option = McqOption::create($validated);

        return redirect()->route('mcq-blocks.mcq-options.index', $mcqBlock)
                         ->with('success', 'Option added successfully.');
    }

    /**
     * Display the specified option.
     */
    public function show(McqBlock $mcqBlock, McqOption $mcqOption)
    {
        return view('mcq_options.show', compact('mcqBlock', 'mcqOption'));
    }

    /**
     * Show the form for editing the specified option.
     */
    public function edit(McqBlock $mcqBlock, McqOption $mcqOption)
    {
        return view('mcq_options.edit', compact('mcqBlock', 'mcqOption'));
    }

    /**
     * Update the specified option in the database.
     */
    public function update(Request $request, McqBlock $mcqBlock, McqOption $mcqOption)
    {
        $validated = $request->validate([
            'label'            => 'required|string|max:10',
            'option_text'      => 'nullable|string',
            'option_image_url' => 'nullable|url',
            'option_cells'     => 'nullable|array',
            'sort_order'       => 'required|integer|min:0',
        ]);

        $mcqOption->update($validated);

        return redirect()->route('mcq-blocks.mcq-options.index', $mcqBlock)
                         ->with('success', 'Option updated successfully.');
    }

    /**
     * Remove the specified option.
     */
    public function destroy(McqBlock $mcqBlock, McqOption $mcqOption)
    {
        $mcqOption->delete();

        return redirect()->route('mcq-blocks.mcq-options.index', $mcqBlock)
                         ->with('success', 'Option deleted successfully.');
    }
}
