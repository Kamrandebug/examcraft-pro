<?php

namespace App\Http\Controllers;

use App\Models\QuestionBank;
use App\Models\ExamPaper;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        $totalPapers = ExamPaper::count();
        $totalQuestions = QuestionBank::count();
        $totalUsers = User::count();
        $pendingExports = 0; // Placeholder for now

        $recentPapers = ExamPaper::latest()->limit(5)->get();
        $recentQuestions = QuestionBank::latest()->limit(5)->get();

        return view('admin.dashboard', compact(
            'totalPapers', 
            'totalQuestions', 
            'totalUsers', 
            'pendingExports',
            'recentPapers',
            'recentQuestions'
        ));
    }
}
