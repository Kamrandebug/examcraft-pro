<?php

namespace App\Http\Controllers;

use App\Models\ExamPaperTopic;
use App\Models\ExamPaper;
use Illuminate\Http\Request;

class ExamPaperTopicController extends Controller
{
    /**
     * Display all topics for a given exam paper.
     */
    public function index(ExamPaper $examPaper)
    {
        $topics = $examPaper->topics;

        return view('exam_paper_topics.index', compact('examPaper', 'topics'));
    }

    /**
     * Show the form for creating a new topic.
     */
    public function create(ExamPaper $examPaper)
    {
        return view('exam_paper_topics.create', compact('examPaper'));
    }

    /**
     * Store a newly created topic in the database.
     */
    public function store(Request $request, ExamPaper $examPaper)
    {
        $validated = $request->validate([
            'topic_name'     => 'required|string|max:255',
            'chapter'        => 'nullable|string|max:255',
            'question_count' => 'nullable|integer|min:0',
        ]);

        $validated['exam_paper_id'] = $examPaper->id;

        $topic = ExamPaperTopic::create($validated);

        return redirect()->route('exam-papers.topics.index', $examPaper)
                         ->with('success', 'Topic added successfully.');
    }

    /**
     * Display the specified topic.
     */
    public function show(ExamPaper $examPaper, ExamPaperTopic $topic)
    {
        return view('exam_paper_topics.show', compact('examPaper', 'topic'));
    }

    /**
     * Show the form for editing the specified topic.
     */
    public function edit(ExamPaper $examPaper, ExamPaperTopic $topic)
    {
        return view('exam_paper_topics.edit', compact('examPaper', 'topic'));
    }

    /**
     * Update the specified topic in the database.
     */
    public function update(Request $request, ExamPaper $examPaper, ExamPaperTopic $topic)
    {
        $validated = $request->validate([
            'topic_name'     => 'required|string|max:255',
            'chapter'        => 'nullable|string|max:255',
            'question_count' => 'nullable|integer|min:0',
        ]);

        $topic->update($validated);

        return redirect()->route('exam-papers.topics.index', $examPaper)
                         ->with('success', 'Topic updated successfully.');
    }

    /**
     * Remove the specified topic.
     */
    public function destroy(ExamPaper $examPaper, ExamPaperTopic $topic)
    {
        $topic->delete();

        return redirect()->route('exam-papers.topics.index', $examPaper)
                         ->with('success', 'Topic deleted successfully.');
    }
}
