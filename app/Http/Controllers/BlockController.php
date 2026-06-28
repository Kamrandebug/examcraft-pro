<?php

namespace App\Http\Controllers;

use App\Models\Block;
use App\Models\Page;
use App\Models\ExamPaper;
use Illuminate\Http\Request;

class BlockController extends Controller
{
    /**
     * Display all blocks for a given page.
     */
    public function index(ExamPaper $examPaper, Page $page)
    {
        $blocks = $page->blocks()->with('mcqBlock.options')->get();

        return view('blocks.index', compact('examPaper', 'page', 'blocks'));
    }

    /**
     * Show the form for creating a new block.
     */
    public function create(ExamPaper $examPaper, Page $page)
    {
        return view('blocks.create', compact('examPaper', 'page'));
    }

    /**
     * Store a newly created block in the database.
     */
    public function store(Request $request, ExamPaper $examPaper, Page $page)
    {
        $validated = $request->validate([
            'block_type'     => 'required|string|in:mcq,section,text,image,table,divider',
            'sort_order'     => 'required|integer|min:0',
            'display_number' => 'nullable|integer',
            'content'        => 'nullable|array',
        ]);

        $validated['page_id'] = $page->id;

        $block = Block::create($validated);

        return redirect()->route('exam-papers.pages.blocks.show', [$examPaper, $page, $block])
                         ->with('success', 'Block added successfully.');
    }

    /**
     * Display the specified block.
     */
    public function show(ExamPaper $examPaper, Page $page, Block $block)
    {
        $block->load('mcqBlock.options');

        return view('blocks.show', compact('examPaper', 'page', 'block'));
    }

    /**
     * Show the form for editing the specified block.
     */
    public function edit(ExamPaper $examPaper, Page $page, Block $block)
    {
        return view('blocks.edit', compact('examPaper', 'page', 'block'));
    }

    /**
     * Update the specified block in the database.
     */
    public function update(Request $request, ExamPaper $examPaper, Page $page, Block $block)
    {
        $validated = $request->validate([
            'block_type'     => 'required|string|in:mcq,section,text,image,table,divider',
            'sort_order'     => 'required|integer|min:0',
            'display_number' => 'nullable|integer',
            'content'        => 'nullable|array',
        ]);

        $block->update($validated);

        return redirect()->route('exam-papers.pages.blocks.show', [$examPaper, $page, $block])
                         ->with('success', 'Block updated successfully.');
    }

    /**
     * Remove the specified block.
     */
    public function destroy(ExamPaper $examPaper, Page $page, Block $block)
    {
        $block->delete();

        return redirect()->route('exam-papers.pages.blocks.index', [$examPaper, $page])
                         ->with('success', 'Block deleted successfully.');
    }
}
