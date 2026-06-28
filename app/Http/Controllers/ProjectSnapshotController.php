<?php

namespace App\Http\Controllers;

use App\Models\ProjectSnapshot;
use App\Models\ExamPaper;
use Illuminate\Http\Request;

class ProjectSnapshotController extends Controller
{
    /**
     * Display all snapshots for a given exam paper.
     */
    public function index(ExamPaper $examPaper)
    {
        $snapshots = $examPaper->snapshots()->latest()->get();

        return view('project_snapshots.index', compact('examPaper', 'snapshots'));
    }

    /**
     * Show the form for creating a new snapshot.
     */
    public function create(ExamPaper $examPaper)
    {
        return view('project_snapshots.create', compact('examPaper'));
    }

    /**
     * Store a newly created snapshot in the database.
     */
    public function store(Request $request, ExamPaper $examPaper)
    {
        $validated = $request->validate([
            'version_label' => 'required|string|max:100',
            'full_state'    => 'required|array',
        ]);

        $validated['exam_paper_id'] = $examPaper->id;

        $snapshot = ProjectSnapshot::create($validated);

        return redirect()->route('exam-papers.snapshots.index', $examPaper)
                         ->with('success', 'Snapshot saved successfully.');
    }

    /**
     * Display the specified snapshot.
     */
    public function show(ExamPaper $examPaper, ProjectSnapshot $snapshot)
    {
        return view('project_snapshots.show', compact('examPaper', 'snapshot'));
    }

    /**
     * Show the form for editing the specified snapshot.
     */
    public function edit(ExamPaper $examPaper, ProjectSnapshot $snapshot)
    {
        return view('project_snapshots.edit', compact('examPaper', 'snapshot'));
    }

    /**
     * Update the specified snapshot in the database.
     */
    public function update(Request $request, ExamPaper $examPaper, ProjectSnapshot $snapshot)
    {
        $validated = $request->validate([
            'version_label' => 'required|string|max:100',
            'full_state'    => 'required|array',
        ]);

        $snapshot->update($validated);

        return redirect()->route('exam-papers.snapshots.index', $examPaper)
                         ->with('success', 'Snapshot updated successfully.');
    }

    /**
     * Remove the specified snapshot.
     */
    public function destroy(ExamPaper $examPaper, ProjectSnapshot $snapshot)
    {
        $snapshot->delete();

        return redirect()->route('exam-papers.snapshots.index', $examPaper)
                         ->with('success', 'Snapshot deleted successfully.');
    }
}
