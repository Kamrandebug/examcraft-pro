<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserPaper;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminSpaApiController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    /**
     * Verify admin and paper ownership.
     */
    private function verifyAdminAccess(User $user, ?UserPaper $paper = null): void
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized');
        }

        if ($paper && $paper->user_id !== $user->id) {
            abort(404);
        }
    }

    /**
     * Create a new paper for a target user (admin context).
     */
    public function store(User $user, Request $request): JsonResponse
    {
        $this->verifyAdminAccess($user);

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
            'user_id'     => $user->id,
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

    /**
     * Update paper for a target user (admin context).
     */
    public function update(User $user, UserPaper $paper, Request $request): JsonResponse
    {
        $this->verifyAdminAccess($user, $paper);

        $validated = $request->validate([
            'title'       => 'sometimes|nullable|string|max:255',
            'grade'       => 'sometimes|nullable|string|max:50',
            'subject'     => 'sometimes|nullable|string|max:100',
            'school_name' => 'sometimes|nullable|string|max:255',
            'exam_date'   => 'sometimes|nullable|date',
            'paper_data'  => 'sometimes|nullable|array',
            'status'      => 'sometimes|in:draft,published',
        ]);

        $paper->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Paper updated successfully.',
            'paper'   => $paper->fresh(),
        ]);
    }

    /**
     * Retrieve paper for a target user (admin context).
     */
    public function show(User $user, UserPaper $paper): JsonResponse
    {
        $this->verifyAdminAccess($user, $paper);

        return response()->json([
            'success'    => true,
            'paper'      => $paper,
            'paper_data' => $paper->paper_data,
        ]);
    }
}
