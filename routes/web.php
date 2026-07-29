<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ExamPaperController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\BlockController;
use App\Http\Controllers\McqBlockController;
use App\Http\Controllers\McqOptionController;
use App\Http\Controllers\ExamPaperTopicController;
use App\Http\Controllers\QuestionBankController;
use App\Http\Controllers\QuestionBankOptionController;
use App\Http\Controllers\ProjectSnapshotController;
use App\Http\Controllers\AdminController;

/*
|--------------------------------------------------------------------------
| Web Routes — ExamCraft Pro
|--------------------------------------------------------------------------
|
| Route structure mirrors the DB relationships:
|
|   users
|   exam-papers
|     └── pages
|           └── blocks
|     └── topics
|     └── snapshots
|   mcq-blocks
|     └── mcq-options
|   question-bank
|     └── options
|
*/

// ── Admin Dashboard (Temporarily public) ───────────────────────────────────
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])
         ->name('dashboard');
    Route::resource('questions', QuestionBankController::class);
    
    // Global Options Management
    Route::get('options', [QuestionBankOptionController::class, 'index'])->name('questions.options.index');
    Route::post('options', [QuestionBankOptionController::class, 'store'])->name('questions.options.store');
    
    Route::resource('papers', ExamPaperController::class);
});

// ── Auth-protected routes ─────────────────────────────────────────────────
Route::middleware(['auth'])->group(function () {

    // ── Users ──────────────────────────────────────────────────────────────
    Route::resource('users', UserController::class);

    // ── Exam Papers (top-level) ────────────────────────────────────────────
    Route::resource('exam-papers', ExamPaperController::class);

    // ── Pages  (nested under exam-papers) ─────────────────────────────────
    Route::resource('exam-papers.pages', PageController::class);

    // ── Blocks (nested under exam-papers → pages) ─────────────────────────
    Route::resource('exam-papers.pages.blocks', BlockController::class);

    // ── Topics (nested under exam-papers) ─────────────────────────────────
    Route::resource('exam-papers.topics', ExamPaperTopicController::class);

    // ── Project Snapshots (nested under exam-papers) ──────────────────────
    Route::resource('exam-papers.snapshots', ProjectSnapshotController::class);

    // ── MCQ Blocks (standalone — referenced by block_id) ──────────────────
    Route::resource('mcq-blocks', McqBlockController::class);

    // ── MCQ Options (nested under mcq-blocks) ─────────────────────────────
    Route::resource('mcq-blocks.mcq-options', McqOptionController::class);

    // ── Question Bank (top-level) ──────────────────────────────────────────
    Route::resource('question-bank', QuestionBankController::class);

    // ── Question Bank Options (nested under question-bank) ────────────────
    Route::resource('question-bank.options', QuestionBankOptionController::class);

});

// ── SPA catch-all (must be last — matches any URL that didn't hit a route above) ─
Route::get('/{any?}', function () {
    return view('app');
})->where('any', '.*');
