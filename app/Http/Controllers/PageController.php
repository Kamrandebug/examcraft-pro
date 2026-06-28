<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Models\ExamPaper;
use Illuminate\Http\Request;

class PageController extends Controller
{
    /**
     * Display all pages for a given exam paper.
     */
    public function index(ExamPaper $examPaper)
    {
        $pages = $examPaper->pages()->with('blocks')->orderBy('page_number')->get();

        return view('pages.index', compact('examPaper', 'pages'));
    }

    /**
     * Show the form for creating a new page.
     */
    public function create(ExamPaper $examPaper)
    {
        return view('pages.create', compact('examPaper'));
    }

    /**
     * Store a newly created page in the database.
     */
    public function store(Request $request, ExamPaper $examPaper)
    {
        $validated = $request->validate([
            'page_number' => 'required|integer|min:1',
        ]);

        $validated['exam_paper_id'] = $examPaper->id;

        $page = Page::create($validated);

        return redirect()->route('exam-papers.pages.show', [$examPaper, $page])
                         ->with('success', 'Page added successfully.');
    }

    /**
     * Display the specified page with its blocks.
     */
    public function show(ExamPaper $examPaper, Page $page)
    {
        $page->load('blocks.mcqBlock.options');

        return view('pages.show', compact('examPaper', 'page'));
    }

    /**
     * Show the form for editing the specified page.
     */
    public function edit(ExamPaper $examPaper, Page $page)
    {
        return view('pages.edit', compact('examPaper', 'page'));
    }

    /**
     * Update the specified page in the database.
     */
    public function update(Request $request, ExamPaper $examPaper, Page $page)
    {
        $validated = $request->validate([
            'page_number' => 'required|integer|min:1',
        ]);

        $page->update($validated);

        return redirect()->route('exam-papers.pages.show', [$examPaper, $page])
                         ->with('success', 'Page updated successfully.');
    }

    /**
     * Remove the specified page and all its blocks.
     */
    public function destroy(ExamPaper $examPaper, Page $page)
    {
        $page->delete();

        return redirect()->route('exam-papers.pages.index', $examPaper)
                         ->with('success', 'Page deleted successfully.');
    }
}
