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
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\ExamCraftController;

/*
|--------------------------------------------------------------------------
| Web Routes — ExamCraft Pro
|--------------------------------------------------------------------------
|*/

// ── Authentication ────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLogin'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/register', [RegisterController::class, 'showRegister'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// ── Admin Dashboard (Auth + Admin Middleware) ──────────────────────────────
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::resource('questions', QuestionBankController::class);

    Route::resource('papers', ExamPaperController::class);
    Route::resource('users', UserController::class);
});

// ── Auth-protected API/SPA routes ─────────────────────────────────────────
Route::middleware(['auth'])->group(function () {

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

    // ── SPA Routes (Inside auth group) ────────────────────────────────────
    Route::get('/', [ExamCraftController::class, 'index'])->name('home');
    Route::get('/{any}', [ExamCraftController::class, 'index'])->where('any', '.*');

});
