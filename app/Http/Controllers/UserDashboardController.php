<?php

namespace App\Http\Controllers;

use App\Models\UserPaper;
use Illuminate\Support\Facades\Auth;

class UserDashboardController extends Controller
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
     * Show the user-scoped dashboard with paper stats.
     */
    public function index()
    {
        $userId = Auth::id();

        $totalPapers     = UserPaper::forUser($userId)->count();
        $autoPapers      = UserPaper::forUser($userId)->where('type', 'auto')->count();
        $manualPapers    = UserPaper::forUser($userId)->where('type', 'manual')->count();
        $publishedPapers = UserPaper::forUser($userId)->where('status', 'published')->count();

        $recentPapers = UserPaper::forUser($userId)
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        return view('user.dashboard', compact(
            'totalPapers', 'autoPapers', 'manualPapers', 'publishedPapers', 'recentPapers'
        ));
    }
}
