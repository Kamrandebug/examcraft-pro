<?php

namespace App\Http\Controllers;

use App\Models\McqBlock;
use App\Models\Block;
use Illuminate\Http\Request;

class McqBlockController extends Controller
{
    /**
     * Display a listing of all MCQ blocks.
     */
    public function index()
    {
        $mcqBlocks = McqBlock::with('block.page.examPaper')->latest()->paginate(20);

        return view('mcq_blocks.index', compact('mcqBlocks'));
    }

    /**
     * Show the form for creating a new MCQ block.
     */
    public function create()
    {
        $blocks = Block::where('block_type', 'mcq')->doesntHave('mcqBlock')->get();

        return view('mcq_blocks.create', compact('blocks'));
    }

    /**
     * Store a newly created MCQ block in the database.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'block_id'             => 'required|exists:blocks,id|unique:mcq_blocks,block_id',
            'stem'                 => 'required|string',
            'stem_image_url'       => 'nullable|url',
            'option_type'          => 'nullable|string|max:50',
            'option_table_headers' => 'nullable|array',
            'marks'                => 'nullable|integer|min:0',
            'layout'               => 'nullable|string|in:1col,2col,inline',
            'correct_answer'       => 'nullable|string|max:10',
            'show_answer_boxes'    => 'boolean',
        ]);

        $mcqBlock = McqBlock::create($validated);

        return redirect()->route('mcq-blocks.show', $mcqBlock)
                         ->with('success', 'MCQ block created successfully.');
    }

    /**
     * Display the specified MCQ block with its options.
     */
    public function show(McqBlock $mcqBlock)
    {
        $mcqBlock->load('options', 'block.page.examPaper');

        return view('mcq_blocks.show', compact('mcqBlock'));
    }

    /**
     * Show the form for editing the specified MCQ block.
     */
    public function edit(McqBlock $mcqBlock)
    {
        return view('mcq_blocks.edit', compact('mcqBlock'));
    }

    /**
     * Update the specified MCQ block in the database.
     */
    public function update(Request $request, McqBlock $mcqBlock)
    {
        $validated = $request->validate([
            'stem'                 => 'required|string',
            'stem_image_url'       => 'nullable|url',
            'option_type'          => 'nullable|string|max:50',
            'option_table_headers' => 'nullable|array',
            'marks'                => 'nullable|integer|min:0',
            'layout'               => 'nullable|string|in:1col,2col,inline',
            'correct_answer'       => 'nullable|string|max:10',
            'show_answer_boxes'    => 'boolean',
        ]);

        $mcqBlock->update($validated);

        return redirect()->route('mcq-blocks.show', $mcqBlock)
                         ->with('success', 'MCQ block updated successfully.');
    }

    /**
     * Remove the specified MCQ block and its options.
     */
    public function destroy(McqBlock $mcqBlock)
    {
        $mcqBlock->delete();

        return redirect()->route('mcq-blocks.index')
                         ->with('success', 'MCQ block deleted successfully.');
    }
}
