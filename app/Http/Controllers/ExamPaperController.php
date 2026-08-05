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

        $papers = $query->paginate(15);

        return view('admin.papers.index', compact('papers'));
    }

    /**
     * Show the form for creating a new exam paper.
     */
    public function create()
    {
        return view('admin.papers.create');
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

        return redirect()->route('admin.papers.show', $examPaper)
                         ->with('success', 'Exam paper created successfully.');
    }

    /**
     * Display the specified exam paper with its pages and blocks.
     */
    public function show(ExamPaper $paper)
    {
        $paper->load(['pages.blocks.mcqBlock.options', 'topics', 'snapshots']);

        return view('admin.papers.show', compact('paper'));
    }

    /**
     * Show the form for editing the specified exam paper.
     */
    public function edit(ExamPaper $paper)
    {
        return view('admin.papers.edit', compact('paper'));
    }

    /**
     * Update the specified exam paper in the database.
     */
    public function update(Request $request, ExamPaper $paper)
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

        $paper->update($validated);

        return redirect()->route('admin.papers.show', $paper)
                         ->with('success', 'Exam paper updated successfully.');
    }

    /**
     * Remove the specified exam paper and all its related data.
     */
    public function destroy(ExamPaper $paper)
    {
        $paper->delete();

        return redirect()->route('admin.papers.index')
                         ->with('success', 'Exam paper deleted successfully.');
    }
}
