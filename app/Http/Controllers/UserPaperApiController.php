<?php

namespace App\Http\Controllers;

use App\Models\UserPaper;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserPaperApiController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    public function index(): JsonResponse
    {
        $papers = UserPaper::forUser(Auth::id())
            ->select(['id', 'title', 'type', 'grade', 'subject', 'status', 'created_at'])
            ->orderByDesc('created_at')
            ->get();

        return response()->json(['success' => true, 'papers' => $papers]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'type'        => 'required|in:manual,auto',
            'grade'       => 'nullable|string|max:50',
            'subject'     => 'nullable|string|max:100',
            'school_name' => 'nullable|string|max:255',
            'exam_date'   => 'nullable|date',
            'paper_data'  => 'required|array',
            'status'      => 'nullable|in:draft,published',
        ]);

        $paper = UserPaper::create([
            'user_id'     => Auth::id(),
            'title'       => $validated['title'],
            'type'        => $validated['type'],
            'grade'       => $validated['grade'] ?? null,
            'subject'     => $validated['subject'] ?? null,
            'school_name' => $validated['school_name'] ?? null,
            'exam_date'   => $validated['exam_date'] ?? null,
            'paper_data'  => $validated['paper_data'],
            'status'      => $validated['status'] ?? 'draft',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Paper saved successfully.',
            'paper'   => [
                'id'         => $paper->id,
                'title'      => $paper->title,
                'type'       => $paper->type,
                'status'     => $paper->status,
                'created_at' => $paper->created_at->toDateTimeString(),
            ],
        ], 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $paper = UserPaper::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $validated = $request->validate([
            'title'       => 'sometimes|required|string|max:255',
            'grade'       => 'sometimes|nullable|string|max:50',
            'subject'     => 'sometimes|nullable|string|max:100',
            'school_name' => 'sometimes|nullable|string|max:255',
            'exam_date'   => 'sometimes|nullable|date',
            'paper_data'  => 'sometimes|required|array',
            'status'      => 'sometimes|in:draft,published',
        ]);

        $paper->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Paper updated.',
            'paper'   => ['id' => $paper->id, 'title' => $paper->title],
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $paper = UserPaper::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        return response()->json([
            'success'    => true,
            'paper'      => $paper,
            'paper_data' => $paper->paper_data,
        ]);
    }
}
