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
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserDashboardController;
use App\Http\Controllers\UserPaperController;
use App\Http\Controllers\UserPaperApiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes — ExamCraft Pro
|--------------------------------------------------------------------------
| Breeze auth routes are in routes/auth.php — DO NOT edit that file.
|*/

// ── Root Route: landing for guests, role-aware dashboard for authenticated ──
Route::get('/', function () {
    if (! auth()->check()) {
        return view('landing');
    }

    if (auth()->user()->isAdmin()) {
        return redirect()->route('admin.dashboard');
    }

    return redirect()->route('user.dashboard');
})->name('home');

// ── Breeze Dashboard → redirect to SPA ────────────────────────────────────
Route::get('/dashboard', fn () => redirect('/'))->middleware('auth')->name('dashboard');

// ── SPA launchers (clean URLs — the route name carries the initial view) ──
Route::middleware('auth')->group(function () {
    Route::get('/user/manual', fn () => view('app', ['initialMode' => 'manual']))->name('user.manual');
    Route::get('/user/auto', fn () => view('app', ['initialMode' => 'auto']))->name('user.auto');
});

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
    Route::resource('papers', ExamPaperController::class);
    Route::resource('papers.pages', PageController::class);
    Route::resource('papers.pages.blocks', BlockController::class);
    Route::resource('mcq-blocks', McqBlockController::class);
    Route::resource('mcq-blocks.mcq-options', McqOptionController::class);
    Route::resource('papers.topics', ExamPaperTopicController::class);
});

// ── SPA API Routes (Auth protected, session cookie) ───────────────────────
Route::get('/api/question-bank/filter', [QuestionBankController::class, 'filter'])->middleware('auth');

// User Paper API (called from Vue SPA — session auth + CSRF)
Route::middleware('auth')->prefix('api/user')->name('api.user.')->group(function () {
    Route::get('papers', [UserPaperApiController::class, 'index'])->name('papers.index');
    Route::post('papers', [UserPaperApiController::class, 'store'])->name('papers.store');
    Route::get('papers/{id}', [UserPaperApiController::class, 'show'])->name('papers.show');
    Route::put('papers/{id}', [UserPaperApiController::class, 'update'])->name('papers.update');
});

// ── SPA Catch-All Route (Auth protected) ──────────────────────────────────
Route::middleware(['auth'])->group(function () {
    Route::get('/app/{any?}', [ExamCraftController::class, 'index'])
        ->where('any', '.*')
        ->name('examcraft');
});

// ── User Dashboard (non-admin users only) ─────────────────────────────────
Route::prefix('user')->middleware(['auth'])->name('user.')->group(function () {
    Route::get('dashboard', [UserDashboardController::class, 'index'])->name('dashboard');

    Route::get('papers', [UserPaperController::class, 'index'])->name('papers.index');
    Route::get('papers/{id}', [UserPaperController::class, 'show'])->name('papers.show');
    Route::get('papers/{id}/edit', [UserPaperController::class, 'edit'])->name('papers.edit');
    Route::put('papers/{id}', [UserPaperController::class, 'update'])->name('papers.update');
    Route::delete('papers/{id}', [UserPaperController::class, 'destroy'])->name('papers.destroy');
    Route::get('papers/{id}/export', [UserPaperController::class, 'export'])->name('papers.export');
});

// ── Breeze Auth Routes (routes/auth.php) ──────────────────────────────────
require __DIR__.'/auth.php';
