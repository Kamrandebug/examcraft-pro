<?php

namespace App\Http\Controllers;

use App\Models\ExamPaper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExamPaperController extends Controller
{
    /**
     * Display a listing of all exam papers for the authenticated user.
     */
    public function index()
    {
        $query = ExamPaper::latest();
        
        if (Auth::check()) {
            $query->where('user_id', Auth::id());
        }

        $examPapers = $query->paginate(15);

        return view('exam_papers.index', compact('examPapers'));
    }

    /**
     * Show the form for creating a new exam paper.
     */
    public function create()
    {
        return view('exam_papers.create');
    }

    /**
     * Store a newly created exam paper in the database.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'              => 'required|string|max:255',
            'subject'            => 'nullable|string|max:255',
            'exam_code'          => 'nullable|string|max:50',
            'organization'       => 'nullable|string|max:255',
            'session'            => 'nullable|string|max:50',
            'year'               => 'nullable|digits:4',
            'duration'           => 'nullable|string|max:50',
            'instructions'       => 'nullable|string',
            'materials'          => 'nullable|string',
            'typography_preset'  => 'nullable|string|max:100',
            'status'             => 'nullable|string|max:50',
        ]);

        $validated['user_id'] = Auth::id() ?? (\App\Models\User::first()->id ?? 1);

        $examPaper = ExamPaper::create($validated);

        return redirect()->route('admin.papers.index')
                         ->with('success', 'Exam paper created successfully.');
    }

    /**
     * Display the specified exam paper with its pages and blocks.
     */
    public function show(ExamPaper $examPaper)
    {
        $examPaper->load(['pages.blocks.mcqBlock.options', 'topics', 'snapshots']);

        return view('exam_papers.show', compact('examPaper'));
    }

    /**
     * Show the form for editing the specified exam paper.
     */
    public function edit(ExamPaper $examPaper)
    {
        return view('exam_papers.edit', compact('examPaper'));
    }

    /**
     * Update the specified exam paper in the database.
     */
    public function update(Request $request, ExamPaper $examPaper)
    {
        $validated = $request->validate([
            'title'              => 'required|string|max:255',
            'subject'            => 'nullable|string|max:255',
            'exam_code'          => 'nullable|string|max:50',
            'organization'       => 'nullable|string|max:255',
            'session'            => 'nullable|string|max:50',
            'year'               => 'nullable|digits:4',
            'duration'           => 'nullable|string|max:50',
            'instructions'       => 'nullable|string',
            'materials'          => 'nullable|string',
            'typography_preset'  => 'nullable|string|max:100',
            'typography_state'   => 'nullable|array',
            'style_state'        => 'nullable|array',
            'cover_footer'       => 'nullable|array',
            'page_footer'        => 'nullable|array',
            'status'             => 'nullable|string|max:50',
        ]);

        $examPaper->update($validated);

        return redirect()->route('admin.papers.index')
                         ->with('success', 'Exam paper updated successfully.');
    }

    /**
     * Remove the specified exam paper and all its related data.
     */
    public function destroy(ExamPaper $examPaper)
    {
        $examPaper->delete();

        return redirect()->route('admin.papers.index')
                         ->with('success', 'Exam paper deleted successfully.');
    }
}
