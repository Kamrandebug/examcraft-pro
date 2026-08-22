<?php

namespace App\Http\Controllers;

use App\Models\UserPaper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserPaperController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
        $this->middleware(function ($request, $next) {
            if (Auth::user()->isAdmin()) {
                return redirect('/');
            }

            return $next($request);
        });
    }

    /**
     * A paper owned by the authenticated user, or 404.
     */
    private function authorizedPaper(int $id): UserPaper
    {
        return UserPaper::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();
    }

    public function index()
    {
        $papers = UserPaper::forUser(Auth::id())
            ->orderByDesc('created_at')
            ->get();

        return view('user.papers.index', compact('papers'));
    }

    public function show(int $id)
    {
        $paper = $this->authorizedPaper($id);

        return view('user.papers.show', compact('paper'));
    }

    public function update(Request $request, int $id)
    {
        $paper = $this->authorizedPaper($id);

        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'grade'       => 'nullable|string|max:50',
            'subject'     => 'nullable|string|max:100',
            'school_name' => 'nullable|string|max:255',
            'exam_date'   => 'nullable|date',
            'status'      => 'required|in:draft,published',
            'paper_code'  => 'nullable|string|max:50',
            'session'     => 'nullable|string|max:50',
            'duration'    => 'nullable|string|max:50',
            'additional_materials' => 'nullable|string',
            'instructions' => 'nullable|string',
        ]);

        // Only actual table columns go into the first update.
        $columnKeys = ['title', 'grade', 'subject', 'school_name', 'exam_date', 'status'];
        $columns = array_intersect_key($validated, array_flip($columnKeys));

        $paper->update($columns);

        // Merge editable auto-paper fields back into the paper_data JSON.
        if ($paper->type === 'auto') {
            $pd = $paper->paper_data ?? [];

            foreach ([
                'paper_code'           => 'paperCode',
                'session'              => 'session',
                'duration'             => 'duration',
                'additional_materials' => 'additionalMaterials',
                'instructions'         => 'instructions',
            ] as $requestKey => $dataKey) {
                if ($request->has($requestKey)) {
                    $pd[$dataKey] = $request->input($requestKey);
                }
            }

            $paper->update(['paper_data' => $pd]);
        }

        return redirect()
            ->route('user.papers.show', $paper->id)
            ->with('success', 'Paper updated successfully.');
    }

    public function destroy(int $id)
    {
        $paper = $this->authorizedPaper($id);
        $paper->delete();

        return redirect()
            ->route('user.papers.index')
            ->with('success', 'Paper deleted successfully.');
    }

    /**
     * Redirect to the SPA with this paper loaded for export/print.
     */
    public function export(int $id)
    {
        $paper = $this->authorizedPaper($id);
        $mode = $paper->type === 'auto' ? 'auto-preview' : 'manual';

        return view('app', [
            'initialMode'    => $mode,
            'initialPaperId' => $paper->id,
        ]);
    }
}
