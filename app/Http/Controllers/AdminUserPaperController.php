<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserPaper;
use Illuminate\Http\Request;

class AdminUserPaperController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    /**
     * Verify that a paper belongs to the target user.
     */
    private function verifyOwnership(UserPaper $paper, User $user): void
    {
        if ($paper->user_id !== $user->id) {
            abort(404);
        }
    }

    /**
     * List all papers for a specific user.
     */
    public function index(User $user)
    {
        $papers = $user->userPapers()
            ->orderByDesc('created_at')
            ->get();

        return view('admin.users.papers.index', compact('user', 'papers'));
    }

    /**
     * Show paper type selector (manual/auto).
     */
    public function create(User $user, Request $request)
    {
        $type = $request->query('type');
        if ($type && in_array($type, ['manual', 'auto'])) {
            return $this->store($user, new Request(['type' => $type]));
        }

        return view('admin.users.papers.create', compact('user'));
    }

    /**
     * Create a new paper for the target user.
     */
    public function store(User $user, Request $request)
    {
        $validated = $request->validate([
            'type'        => 'required|in:manual,auto',
            'title'       => 'nullable|string|max:255',
            'grade'       => 'nullable|string|max:50',
            'subject'     => 'nullable|string|max:100',
            'school_name' => 'nullable|string|max:255',
            'exam_date'   => 'nullable|date',
        ]);

        $paper = UserPaper::create([
            'user_id'     => $user->id,
            'title'       => $validated['title'] ?? 'Untitled Paper',
            'type'        => $validated['type'],
            'grade'       => $validated['grade'] ?? null,
            'subject'     => $validated['subject'] ?? null,
            'school_name' => $validated['school_name'] ?? null,
            'exam_date'   => $validated['exam_date'] ?? null,
            'paper_data'  => $validated['type'] === 'auto'
                ? ['selectedMcqs' => [], 'paperMeta' => []]
                : ['pages' => [['blocks' => []]]],
            'status'      => 'draft',
        ]);

        return redirect()
            ->route('admin.user.papers.edit', [$user->id, $paper->id])
            ->with('success', 'Paper created. Launching editor...');
    }

    /**
     * Show A4 preview of a paper.
     */
    public function show(User $user, UserPaper $paper)
    {
        $this->verifyOwnership($paper, $user);

        $view = view('admin.users.papers.show', compact('user', 'paper'));

        return response($view)
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    /**
     * Redirect to SPA editor with admin context.
     */
    public function edit(User $user, UserPaper $paper)
    {
        $this->verifyOwnership($paper, $user);

        $path = $paper->type === 'auto' ? '/user/auto' : '/user/manual';
        return redirect($path . "?paper_id={$paper->id}&admin_user={$user->id}&admin_name=" . urlencode($user->name));
    }

    /**
     * Update paper metadata.
     */
    public function update(User $user, UserPaper $paper, Request $request)
    {
        $this->verifyOwnership($paper, $user);

        $validated = $request->validate([
            'title'       => 'sometimes|required|string|max:255',
            'grade'       => 'sometimes|nullable|string|max:50',
            'subject'     => 'sometimes|nullable|string|max:100',
            'school_name' => 'sometimes|nullable|string|max:255',
            'exam_date'   => 'sometimes|nullable|date',
            'status'      => 'sometimes|in:draft,published',
        ]);

        $columnKeys = ['title', 'grade', 'subject', 'school_name', 'exam_date', 'status'];
        $columns = array_intersect_key($validated, array_flip($columnKeys));

        $paper->update($columns);

        return redirect()
            ->route('admin.user.papers.index', $user->id)
            ->with('success', 'Paper updated successfully.');
    }

    /**
     * Delete a paper.
     */
    public function destroy(User $user, UserPaper $paper)
    {
        $this->verifyOwnership($paper, $user);

        $paper->delete();

        return redirect()
            ->route('admin.user.papers.index', $user->id)
            ->with('success', 'Paper deleted successfully.');
    }

    /**
     * Toggle paper status between draft and published.
     */
    public function toggleStatus(User $user, UserPaper $paper)
    {
        $this->verifyOwnership($paper, $user);

        $paper->status = $paper->status === 'draft' ? 'published' : 'draft';
        $paper->save();

        return response()->json([
            'success' => true,
            'status'  => $paper->status,
        ]);
    }

    /**
     * Export paper as JSON file.
     */
    public function export(User $user, UserPaper $paper)
    {
        $this->verifyOwnership($paper, $user);

        $filename = "{$paper->title}_{$paper->type}_{$paper->id}.json";
        $filename = str_replace([' ', '/'], '_', $filename);

        return response()->json($paper->paper_data, 200, [], JSON_UNESCAPED_SLASHES)
            ->header('Content-Disposition', "attachment; filename=\"{$filename}\"");
    }
}
