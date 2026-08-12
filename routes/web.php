<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\BlockController;
use App\Http\Controllers\ExamCraftController;
use App\Http\Controllers\ExamPaperController;
use App\Http\Controllers\ExamPaperTopicController;
use App\Http\Controllers\McqBlockController;
use App\Http\Controllers\McqOptionController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QuestionBankController;
use App\Http\Controllers\QuestionBankOptionController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes — ExamCraft Pro
|--------------------------------------------------------------------------
| Breeze auth routes are in routes/auth.php — DO NOT edit that file.
|*/

// ── Root Route: landing page for guests, SPA for authenticated users ──────
Route::get('/', function () {
    if (auth()->check()) {
        return view('app');
    }
    return view('landing');
})->name('home');

// ── Breeze Dashboard → redirect to SPA ────────────────────────────────────
Route::get('/dashboard', fn () => redirect('/'))->middleware('auth')->name('dashboard');

// ── Breeze Profile Routes ─────────────────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ── Admin Panel (Auth + Admin Middleware) ─────────────────────────────────
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/', fn () => redirect()->route('admin.dashboard'));
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    Route::resource('users', UserController::class);
    Route::resource('questions', QuestionBankController::class);
    Route::resource('questions.options', QuestionBankOptionController::class);
    Route::resource('papers', ExamPaperController::class);
    Route::resource('papers.pages', PageController::class);
    Route::resource('papers.pages.blocks', BlockController::class);
    Route::resource('mcq-blocks', McqBlockController::class);
    Route::resource('mcq-blocks.mcq-options', McqOptionController::class);
    Route::resource('papers.topics', ExamPaperTopicController::class);
});

// ── SPA API Routes (Auth protected, session cookie) ───────────────────────
Route::get('/api/question-bank/filter', [QuestionBankController::class, 'filter'])->middleware('auth');

// ── SPA Catch-All Route (Auth protected) ──────────────────────────────────
Route::middleware(['auth'])->group(function () {
    Route::get('/app/{any?}', [ExamCraftController::class, 'index'])
        ->where('any', '.*')
        ->name('examcraft');
});

// ── Breeze Auth Routes (routes/auth.php) ──────────────────────────────────
require __DIR__.'/auth.php';
