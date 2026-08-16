# ExamCraft Pro — User Dashboard & Server-Persisted Papers
## Claude Code Implementation Prompt (Production-Level)

---

## 1. PROJECT CONTEXT

You are working on **ExamCraft Pro v36** — a Laravel 12 + Vue 3 hybrid exam authoring platform.

### Tech Stack
- **Backend**: Laravel 12, MySQL, Eloquent ORM, Laravel Breeze v2.4.2 (auth)
- **Frontend SPA**: Vue 3 (Composition API), Pinia (5 stores), Vite, Bootstrap 5
- **Admin Dashboard**: AdminLTE v3.2.0 (Bootstrap 4) at `/admin/*` routes
- **Auth**: Role-based — `admin` and `user` roles stored in a dedicated `roles` table
  - `User` model has `isAdmin()` → checks `$this->role?->name === 'admin'`
  - `AdminMiddleware` protects `/admin/*`

### Current Post-Login Flow (TO BE CHANGED)
```
Login → GET / → ExamCraftController@index → app.blade.php (Vue SPA shell)
Vue SPA → HomeScreen.vue → [Create Manual Paper | Auto Paper Generator]
```
**Problem**: Auto-generated papers are never saved to MySQL. Manual papers only go to IndexedDB. No user-owned paper history exists.

### Existing Key Models (already in codebase)
- `User` (HasOne Role, id, name, email, password, timestamps)
- `Role` (id, user_id, name ['admin'|'user'], timestamps)
- `ExamPaper` (admin-managed papers — title, subject, exam_code, organization, session, year, duration, typo_preset, instructions, materials, status, timestamps)
- `QuestionBank` (id, question_text, image_path, subject, grade, topic, marks, difficulty, type, correct_answer, timestamps)
- `ProjectSnapshot` (exists in codebase — used for IndexedDB snapshots)

### Existing Admin Dashboard Pattern
- AdminLTE v3.2.0 assets at `public/adminlte/`
- Blade views at `resources/views/admin/` (dashboard, users/, questions/, papers/)
- Controller: `AdminController.php`, `UserController.php`
- Routes: `Route::prefix('admin')->middleware(['auth', 'admin'])->group(...)` in `routes/web.php`
- DataTables with export (DataTables Buttons plugin, JSZip, pdfmake — all in `public/adminlte/plugins/`)
- SweetAlert2 for delete confirmations (`public/adminlte/plugins/sweetalert2/`)
- Info-Box components for stats on dashboard

### Existing SPA Stores
- `uiStore.js` — `currentView` (home|manual|auto|auto-preview), `setView(view)`
- `examStore.js` — paper metadata, blocks, pages
- `autoPaperStore.js` — `paperTitle`, `schoolName`, `paperDate`, `grade`, `subject`, `selectedMcqs` (array of MCQ objects)
- `projectStore.js` — IndexedDB CRUD, full project snapshots

### Existing SPA Views
- `HomeScreen.vue` — two cards: "Create Manual Paper" → `setView('manual')`, "Auto Paper Generator" → `setView('auto')`
- `AutoPaperGenerator.vue` — form + MCQ selection → navigates to `auto-preview`
- `AutoPaperPreview.vue` — read-only A4 preview with "Print Paper" + "Edit Selection" + "Home"
- `App.vue` — root component, `v-if` chain on `uiStore.currentView`

---

## 2. REQUIREMENTS (What to Build)

1. **User Dashboard** at `/user/dashboard` — AdminLTE-style Blade page (same visual design as admin, user-scoped sidebar)
2. **After login**, non-admin users redirect to `/user/dashboard` instead of `/`
3. **Admins** keep existing flow: redirect to `/` (SPA) after login
4. **Paper Persistence** — both Auto and Manual papers saved to MySQL `user_papers` table, linked to `auth()->id()`
5. **Full CRUD** on `user_papers` in the User Dashboard:
   - **List**: DataTable with search, sort, export (CSV, Excel, PDF, Print)
   - **View**: Show page with paper details and preview data
   - **Edit**: Edit paper metadata (title, subject, school, date, status) via Blade form
   - **Delete**: SweetAlert2 confirm → soft or hard delete
   - **Export/Download**: Button to open the paper in SPA preview mode for printing
6. **No Question Bank access** for users — sidebar does NOT include question bank link
7. **Create buttons** on dashboard open the Vue SPA in the correct mode
8. **Auto Paper flow** saves to server before showing the preview (or adds a "Save to My Papers" button in preview)
9. **Manual Paper flow** adds a "Save to My Papers" button in the SPA topbar

---

## 3. ARCHITECTURE DECISIONS

### Routing Strategy
```
GET  /                    → if admin → SPA (app.blade.php)
                            if user  → redirect('/user/dashboard')
GET  /spa                 → app.blade.php (SPA shell, query param: ?mode=manual|auto)
GET  /user/dashboard      → UserDashboardController@index
GET  /user/papers         → UserPaperController@index  (web — DataTable page)
GET  /user/papers/{id}    → UserPaperController@show   (web — show page)
GET  /user/papers/{id}/edit → UserPaperController@edit (web — edit form)
PUT  /user/papers/{id}    → UserPaperController@update (web — update metadata)
DELETE /user/papers/{id}  → UserPaperController@destroy (web — delete)
GET  /user/papers/{id}/export → UserPaperController@export (web — redirect to SPA with paper loaded)

POST /api/user/papers          → UserPaperApiController@store   (API — save new paper from SPA)
PUT  /api/user/papers/{id}     → UserPaperApiController@update  (API — update paper data from SPA)
GET  /api/user/papers/{id}     → UserPaperApiController@show    (API — load paper into SPA)
```

### Data Model
`user_papers` table stores both manual and auto papers:
```
id, user_id, title, type (manual|auto), grade, subject, school_name, 
exam_date, paper_data (longText JSON), status (draft|published), 
timestamps
```
`paper_data` JSON structure:
- **For auto papers**: `{ "selectedMcqs": [...], "paperTitle": "...", "schoolName": "...", "paperDate": "...", "grade": "...", "subject": "..." }`
- **For manual papers**: Full examStore state snapshot `{ "pages": [...], "blocks": [...], "metadata": {...} }`

### SPA Integration
- Dashboard "Create Manual Paper" → links to `/spa?mode=manual`
- Dashboard "Create Auto Paper" → links to `/spa?mode=auto`
- `app.blade.php` passes `window.initialMode = "{{ request('mode', '') }}"` and `window.initialPaperId = "{{ request('paper_id', '') }}"`
- `App.vue` reads `window.initialMode` on mount and calls `uiStore.setView(window.initialMode || 'home')`
- **Auto paper save**: In `AutoPaperPreview.vue` add "💾 Save to My Papers" button that POSTs to `/api/user/papers` then shows success toast
- **Manual paper save**: Add "💾 Save" button in `TopBar.vue` that POSTs to `/api/user/papers` then updates with PUT on subsequent saves

---

## 4. PHASE 1: DATABASE MIGRATION

### File to CREATE: `database/migrations/YYYY_MM_DD_000001_create_user_papers_table.php`

Use `php artisan make:migration create_user_papers_table` then replace content:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_papers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->enum('type', ['manual', 'auto'])->default('manual');
            $table->string('grade')->nullable();
            $table->string('subject')->nullable();
            $table->string('school_name')->nullable();
            $table->date('exam_date')->nullable();
            $table->longText('paper_data')->nullable(); // JSON
            $table->enum('status', ['draft', 'published'])->default('draft');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_papers');
    }
};
```

Run: `php artisan migrate`

---

## 5. PHASE 2: MODELS

### File to CREATE: `app/Models/UserPaper.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPaper extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'type',
        'grade',
        'subject',
        'school_name',
        'exam_date',
        'paper_data',
        'status',
    ];

    protected $casts = [
        'paper_data' => 'array',
        'exam_date'  => 'date',
    ];

    // Relationships
    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    // Accessors
    public function getQuestionCountAttribute(): int
    {
        if ($this->type === 'auto' && is_array($this->paper_data)) {
            return count($this->paper_data['selectedMcqs'] ?? []);
        }
        if ($this->type === 'manual' && is_array($this->paper_data)) {
            $count = 0;
            foreach ($this->paper_data['pages'] ?? [] as $page) {
                foreach ($page['blocks'] ?? [] as $block) {
                    if (($block['type'] ?? '') === 'mcq') $count++;
                }
            }
            return $count;
        }
        return 0;
    }
}
```

---

## 6. PHASE 3: CONTROLLERS

### 6a. File to CREATE: `app/Http/Controllers/UserDashboardController.php`

```php
<?php

namespace App\Http\Controllers;

use App\Models\UserPaper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
        // Block admins from user dashboard
        $this->middleware(function ($request, $next) {
            if (Auth::user()->isAdmin()) {
                return redirect('/');
            }
            return $next($request);
        });
    }

    public function index()
    {
        $userId = Auth::id();

        $totalPapers    = UserPaper::forUser($userId)->count();
        $autoPapers     = UserPaper::forUser($userId)->where('type', 'auto')->count();
        $manualPapers   = UserPaper::forUser($userId)->where('type', 'manual')->count();
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
```

### 6b. File to CREATE: `app/Http/Controllers/UserPaperController.php`
(Handles Blade web routes — list, show, edit, update, destroy, export)

```php
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

    public function edit(int $id)
    {
        $paper = $this->authorizedPaper($id);
        return view('user.papers.edit', compact('paper'));
    }

    public function update(Request $request, int $id)
    {
        $paper = $this->authorizedPaper($id);

        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'subject'     => 'nullable|string|max:100',
            'school_name' => 'nullable|string|max:255',
            'exam_date'   => 'nullable|date',
            'status'      => 'required|in:draft,published',
        ]);

        $paper->update($validated);

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
     * Redirect to SPA with paper loaded for export/print.
     */
    public function export(int $id)
    {
        $paper = $this->authorizedPaper($id);
        // Open SPA in preview mode with paper ID — SPA will load paper data via API
        $mode = $paper->type === 'auto' ? 'auto-preview' : 'manual';
        return redirect("/spa?mode={$mode}&paper_id={$paper->id}");
    }
}
```

### 6c. File to CREATE: `app/Http/Controllers/Api/UserPaperApiController.php`
(Handles JSON API routes — store, update, show — called from Vue SPA)

```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserPaper;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserPaperApiController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:sanctum'])->except([]);
        // Fallback: also accept session auth (since SPA uses cookies via Axios + CSRF)
        $this->middleware(['auth'])->only([]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title'         => 'required|string|max:255',
            'type'          => 'required|in:manual,auto',
            'grade'         => 'nullable|string|max:50',
            'subject'       => 'nullable|string|max:100',
            'school_name'   => 'nullable|string|max:255',
            'exam_date'     => 'nullable|date',
            'paper_data'    => 'required|array',
            'status'        => 'nullable|in:draft,published',
        ]);

        $paper = UserPaper::create([
            ...$validated,
            'user_id' => Auth::id(),
            'status'  => $validated['status'] ?? 'draft',
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
            'title'      => 'sometimes|required|string|max:255',
            'paper_data' => 'sometimes|required|array',
            'status'     => 'sometimes|in:draft,published',
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

    public function index(): JsonResponse
    {
        $papers = UserPaper::forUser(Auth::id())
            ->select(['id', 'title', 'type', 'grade', 'subject', 'status', 'created_at'])
            ->orderByDesc('created_at')
            ->get();

        return response()->json(['success' => true, 'papers' => $papers]);
    }
}
```

**IMPORTANT**: The API controller uses Laravel's session-based auth (not Sanctum tokens), because the Vue SPA already sends CSRF tokens via `window.axios` (configured in `bootstrap.js`). Route these under `auth` middleware, not `auth:sanctum`.

---

## 7. PHASE 4: ROUTES

### File to MODIFY: `routes/web.php`

**7a. Change the root `GET /` route:**

Find the existing root route block:
```php
Route::get('/', function () {
    if (auth()->check()) {
        return app(ExamCraftController::class)->index();
    }
    return view('landing');
});
```

Replace with:
```php
Route::get('/', function () {
    if (!auth()->check()) {
        return view('landing');
    }
    // Admin → SPA, User → their dashboard
    if (auth()->user()->isAdmin()) {
        return app(\App\Http\Controllers\ExamCraftController::class)->index();
    }
    return redirect()->route('user.dashboard');
});
```

**7b. Add `/spa` route (for launching SPA from dashboard):**
```php
// SPA launcher — called with ?mode=manual or ?mode=auto from user dashboard
Route::get('/spa', function () {
    return app(\App\Http\Controllers\ExamCraftController::class)->index();
})->middleware('auth');
```

**7c. Add User Dashboard web routes:**
```php
Route::prefix('user')->middleware(['auth'])->name('user.')->group(function () {
    Route::get('dashboard', [\App\Http\Controllers\UserDashboardController::class, 'index'])
         ->name('dashboard');

    Route::get('papers', [\App\Http\Controllers\UserPaperController::class, 'index'])
         ->name('papers.index');
    Route::get('papers/{id}', [\App\Http\Controllers\UserPaperController::class, 'show'])
         ->name('papers.show');
    Route::get('papers/{id}/edit', [\App\Http\Controllers\UserPaperController::class, 'edit'])
         ->name('papers.edit');
    Route::put('papers/{id}', [\App\Http\Controllers\UserPaperController::class, 'update'])
         ->name('papers.update');
    Route::delete('papers/{id}', [\App\Http\Controllers\UserPaperController::class, 'destroy'])
         ->name('papers.destroy');
    Route::get('papers/{id}/export', [\App\Http\Controllers\UserPaperController::class, 'export'])
         ->name('papers.export');
});
```

### File to MODIFY: `routes/api.php`

Add user paper API routes (these use session auth via `auth` middleware, same as the existing `/api/question-bank/filter` route):

```php
// User Paper API (called from Vue SPA — uses session auth + CSRF)
Route::middleware(['auth'])->prefix('user')->name('api.user.')->group(function () {
    Route::get('papers', [\App\Http\Controllers\Api\UserPaperApiController::class, 'index'])
         ->name('papers.index');
    Route::post('papers', [\App\Http\Controllers\Api\UserPaperApiController::class, 'store'])
         ->name('papers.store');
    Route::get('papers/{id}', [\App\Http\Controllers\Api\UserPaperApiController::class, 'show'])
         ->name('papers.show');
    Route::put('papers/{id}', [\App\Http\Controllers\Api\UserPaperApiController::class, 'update'])
         ->name('papers.update');
});
```

---

## 8. PHASE 5: AUTH REDIRECT FIX

### File to MODIFY: `app/Http/Controllers/Auth/AuthenticatedSessionController.php`

Find the `store()` method. The current redirect target is `'/'`. Change to role-based redirect:

```php
// In store() method, find the return/redirect line at the end and replace:
return redirect()->intended(
    auth()->user()->isAdmin() ? '/' : route('user.dashboard')
);
```

Also check `RegisteredUserController.php` store() method — new users are always `user` role, so:
```php
// After creating user and logging in, change redirect to:
return redirect(route('user.dashboard'));
```

### File to MODIFY: `bootstrap/app.php`

The `$middleware->redirectUsersTo('/')` line redirects already-authenticated users who try to visit guest pages (like `/login`). Change this to be role-aware by creating a callable:

```php
// Find: $middleware->redirectUsersTo('/');
// Replace with:
$middleware->redirectUsersTo(function ($request) {
    $user = auth()->user();
    if (!$user) return '/';
    return $user->isAdmin() ? '/' : route('user.dashboard');
});
```

---

## 9. PHASE 6: BLADE VIEWS (User Dashboard — AdminLTE Style)

Create the directory structure:
```
resources/views/user/
├── layouts/
│   └── app.blade.php       ← User AdminLTE layout (sidebar + topbar)
├── dashboard.blade.php     ← Main dashboard with stats + recent papers
└── papers/
    ├── index.blade.php     ← DataTable of all papers
    ├── show.blade.php      ← Paper detail view
    └── edit.blade.php      ← Edit paper metadata form
```

### 9a. File to CREATE: `resources/views/user/layouts/app.blade.php`

This is the AdminLTE shell for all user dashboard pages. Model it EXACTLY on the admin layout pattern but with:
- Sidebar: Dashboard + My Papers (NO Question Bank, NO Users Management)
- Title prefix: "ExamCraft Pro"
- User name from `auth()->user()->name`

```blade
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — ExamCraft Pro</title>

    <!-- Google Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=EB+Garamond:wght@700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/fontawesome-free/css/all.min.css') }}">
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
    <!-- AdminLTE -->
    <link rel="stylesheet" href="{{ asset('adminlte/dist/css/adminlte.min.css') }}">

    <style>
        :root {
            --navy: #1B2A4A;
            --gold: #C9A84C;
        }
        .brand-link { background: var(--navy) !important; }
        .brand-link .brand-text { font-family: 'EB Garamond', serif; font-size: 1.3rem; color: var(--gold) !important; }
        .sidebar { background: #1e2d4f !important; }
        .nav-sidebar .nav-link { color: #c8d3e8 !important; }
        .nav-sidebar .nav-link.active, .nav-sidebar .nav-link:hover { color: #fff !important; background: rgba(201,168,76,0.18) !important; border-left: 3px solid var(--gold); }
        .nav-sidebar .nav-icon { color: var(--gold) !important; }
        .main-header.navbar { background: var(--navy) !important; }
        .main-header .navbar-brand, .main-header .nav-link { color: #fff !important; }
        .badge-auto { background: #6f42c1; }
        .badge-manual { background: #17a2b8; }
        .badge-draft { background: #6c757d; }
        .badge-published { background: #28a745; }
        .info-box-icon { display: flex; align-items: center; justify-content: center; }
    </style>

    @stack('styles')
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-dark">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
            </li>
            <li class="nav-item d-none d-sm-inline-block">
                <a href="{{ route('user.dashboard') }}" class="nav-link">My Dashboard</a>
            </li>
        </ul>
        <ul class="navbar-nav ml-auto">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown">
                    <i class="fas fa-user-circle mr-1"></i>{{ auth()->user()->name }}
                </a>
                <div class="dropdown-menu dropdown-menu-right">
                    <a href="{{ route('profile.edit') }}" class="dropdown-item">
                        <i class="fas fa-user-edit mr-2"></i>Profile
                    </a>
                    <div class="dropdown-divider"></div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item text-danger">
                            <i class="fas fa-sign-out-alt mr-2"></i>Logout
                        </button>
                    </form>
                </div>
            </li>
        </ul>
    </nav>

    <!-- Sidebar -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <a href="{{ route('user.dashboard') }}" class="brand-link text-center px-3">
            <span class="brand-text font-weight-light">ExamCraft Pro</span>
        </a>
        <div class="sidebar">
            <div class="user-panel mt-3 pb-3 mb-3 d-flex align-items-center">
                <div class="image">
                    <div class="img-circle" style="width:34px;height:34px;background:var(--gold);border-radius:50%;display:flex;align-items:center;justify-content:center;">
                        <span style="color:#1B2A4A;font-weight:700;font-size:14px;">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                    </div>
                </div>
                <div class="info ml-2">
                    <a href="{{ route('profile.edit') }}" class="d-block" style="color:#c8d3e8;font-size:13px;">{{ auth()->user()->name }}</a>
                    <small style="color:var(--gold);font-size:11px;">Student / Teacher</small>
                </div>
            </div>

            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">

                    <li class="nav-item">
                        <a href="{{ route('user.dashboard') }}" class="nav-link {{ request()->routeIs('user.dashboard') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('user.papers.index') }}" class="nav-link {{ request()->routeIs('user.papers.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-file-alt"></i>
                            <p>My Papers</p>
                        </a>
                    </li>

                    <li class="nav-header" style="color:#546a8c;font-size:10px;letter-spacing:1px;">CREATE NEW</li>

                    <li class="nav-item">
                        <a href="/spa?mode=manual" class="nav-link">
                            <i class="nav-icon fas fa-pencil-alt"></i>
                            <p>Manual Paper</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="/spa?mode=auto" class="nav-link">
                            <i class="nav-icon fas fa-magic"></i>
                            <p>Auto Paper</p>
                        </a>
                    </li>

                </ul>
            </nav>
        </div>
    </aside>

    <!-- Content Wrapper -->
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">@yield('page-title', 'Dashboard')</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('user.dashboard') }}">Home</a></li>
                            @yield('breadcrumb')
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">

                {{-- Flash messages --}}
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
                        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                    </div>
                @endif

                @yield('content')
            </div>
        </section>
    </div>

    <!-- Footer -->
    <footer class="main-footer">
        <strong>ExamCraft Pro</strong> &copy; {{ date('Y') }}
        <div class="float-right d-none d-sm-inline-block">Professional Exam Authoring</div>
    </footer>

</div>

<!-- Scripts -->
<script src="{{ asset('adminlte/plugins/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('adminlte/plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('adminlte/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('adminlte/plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('adminlte/plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>
<script src="{{ asset('adminlte/plugins/jszip/jszip.min.js') }}"></script>
<script src="{{ asset('adminlte/plugins/pdfmake/pdfmake.min.js') }}"></script>
<script src="{{ asset('adminlte/plugins/pdfmake/vfs_fonts.js') }}"></script>
<script src="{{ asset('adminlte/plugins/datatables-buttons/js/buttons.html5.min.js') }}"></script>
<script src="{{ asset('adminlte/plugins/datatables-buttons/js/buttons.print.min.js') }}"></script>
<script src="{{ asset('adminlte/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
<link rel="stylesheet" href="{{ asset('adminlte/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css') }}">
<script src="{{ asset('adminlte/dist/js/adminlte.min.js') }}"></script>

@stack('scripts')
</body>
</html>
```

### 9b. File to CREATE: `resources/views/user/dashboard.blade.php`

```blade
@extends('user.layouts.app')

@section('title', 'My Dashboard')
@section('page-title', 'My Dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('content')

    {{-- Stats Row --}}
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="info-box shadow-sm">
                <span class="info-box-icon bg-info elevation-1">
                    <i class="fas fa-file-alt"></i>
                </span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Papers</span>
                    <span class="info-box-number">{{ $totalPapers }}</span>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="info-box shadow-sm">
                <span class="info-box-icon bg-purple elevation-1">
                    <i class="fas fa-magic"></i>
                </span>
                <div class="info-box-content">
                    <span class="info-box-text">Auto Generated</span>
                    <span class="info-box-number">{{ $autoPapers }}</span>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="info-box shadow-sm">
                <span class="info-box-icon bg-cyan elevation-1">
                    <i class="fas fa-pencil-alt"></i>
                </span>
                <div class="info-box-content">
                    <span class="info-box-text">Manual Papers</span>
                    <span class="info-box-number">{{ $manualPapers }}</span>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="info-box shadow-sm">
                <span class="info-box-icon bg-success elevation-1">
                    <i class="fas fa-check-circle"></i>
                </span>
                <div class="info-box-content">
                    <span class="info-box-text">Published</span>
                    <span class="info-box-number">{{ $publishedPapers }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card card-outline" style="border-top: 3px solid #17a2b8;">
                <div class="card-body text-center py-4">
                    <i class="fas fa-pencil-alt fa-3x mb-3" style="color:#17a2b8;"></i>
                    <h5 class="card-title">Create Manual Paper</h5>
                    <p class="card-text text-muted small">Full design control. Add MCQs, sections, images, tables with drag-and-drop.</p>
                    <a href="/spa?mode=manual" class="btn btn-info px-4">
                        <i class="fas fa-plus mr-1"></i> Start Designing
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card card-outline" style="border-top: 3px solid #6f42c1;">
                <div class="card-body text-center py-4">
                    <i class="fas fa-magic fa-3x mb-3" style="color:#6f42c1;"></i>
                    <h5 class="card-title">Auto Paper Generator</h5>
                    <p class="card-text text-muted small">Select grade and subject, let the system build a formatted paper automatically.</p>
                    <a href="/spa?mode=auto" class="btn btn-purple px-4" style="background:#6f42c1;color:#fff;">
                        <i class="fas fa-magic mr-1"></i> Generate Paper
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Recent Papers Table --}}
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title"><i class="fas fa-history mr-2"></i>Recent Papers</h3>
            <a href="{{ route('user.papers.index') }}" class="btn btn-sm btn-outline-secondary">
                View All <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="thead-light">
                    <tr>
                        <th>Title</th>
                        <th>Type</th>
                        <th>Subject</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentPapers as $paper)
                    <tr>
                        <td>
                            <a href="{{ route('user.papers.show', $paper->id) }}" class="font-weight-bold text-dark">
                                {{ $paper->title }}
                            </a>
                        </td>
                        <td>
                            <span class="badge badge-{{ $paper->type === 'auto' ? 'auto' : 'manual' }}">
                                {{ ucfirst($paper->type) }}
                            </span>
                        </td>
                        <td>{{ $paper->subject ?? '—' }}</td>
                        <td>
                            <span class="badge badge-{{ $paper->status }}">{{ ucfirst($paper->status) }}</span>
                        </td>
                        <td>{{ $paper->created_at->diffForHumans() }}</td>
                        <td class="text-center">
                            <a href="{{ route('user.papers.show', $paper->id) }}" class="btn btn-xs btn-info" title="View">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('user.papers.edit', $paper->id) }}" class="btn btn-xs btn-warning" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="{{ route('user.papers.export', $paper->id) }}" class="btn btn-xs btn-success" title="Export">
                                <i class="fas fa-print"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">
                            <i class="fas fa-file-alt fa-2x mb-2 d-block"></i>
                            No papers yet. Create your first paper above!
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@endsection
```

### 9c. File to CREATE: `resources/views/user/papers/index.blade.php`

Full DataTable view with search, sort, export (Copy/CSV/Excel/PDF/Print), and SweetAlert2 delete:

```blade
@extends('user.layouts.app')

@section('title', 'My Papers')
@section('page-title', 'My Papers')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('user.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">My Papers</li>
@endsection

@section('content')
<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title"><i class="fas fa-file-alt mr-2"></i>All My Papers</h3>
        <div>
            <a href="/spa?mode=manual" class="btn btn-sm btn-info mr-1">
                <i class="fas fa-pencil-alt mr-1"></i>Manual
            </a>
            <a href="/spa?mode=auto" class="btn btn-sm btn-purple" style="background:#6f42c1;color:#fff;">
                <i class="fas fa-magic mr-1"></i>Auto
            </a>
        </div>
    </div>
    <div class="card-body">
        <table id="papersTable" class="table table-bordered table-striped table-hover">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Title</th>
                    <th>Type</th>
                    <th>Grade</th>
                    <th>Subject</th>
                    <th>School</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th class="text-center no-export">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($papers as $paper)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>
                        <a href="{{ route('user.papers.show', $paper->id) }}" class="font-weight-bold text-dark">
                            {{ $paper->title }}
                        </a>
                    </td>
                    <td>
                        <span class="badge badge-{{ $paper->type === 'auto' ? 'auto' : 'manual' }}">
                            <i class="fas fa-{{ $paper->type === 'auto' ? 'magic' : 'pencil-alt' }} mr-1"></i>
                            {{ ucfirst($paper->type) }}
                        </span>
                    </td>
                    <td>{{ $paper->grade ?? '—' }}</td>
                    <td>{{ $paper->subject ?? '—' }}</td>
                    <td>{{ $paper->school_name ?? '—' }}</td>
                    <td>
                        <span class="badge badge-{{ $paper->status }}">{{ ucfirst($paper->status) }}</span>
                    </td>
                    <td>{{ $paper->created_at->format('d M Y') }}</td>
                    <td class="text-center">
                        <a href="{{ route('user.papers.show', $paper->id) }}" class="btn btn-xs btn-info" title="View">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('user.papers.edit', $paper->id) }}" class="btn btn-xs btn-warning" title="Edit Metadata">
                            <i class="fas fa-edit"></i>
                        </a>
                        <a href="{{ route('user.papers.export', $paper->id) }}" class="btn btn-xs btn-success" title="Print / Export">
                            <i class="fas fa-print"></i>
                        </a>
                        <form method="POST" action="{{ route('user.papers.destroy', $paper->id) }}" class="d-inline delete-form">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-xs btn-danger btn-delete" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(function () {
    $('#papersTable').DataTable({
        order: [[7, 'desc']],
        columnDefs: [{ orderable: false, targets: [8] }],
        dom: 'Bfrtip',
        buttons: [
            { extend: 'copy',  exportOptions: { columns: ':not(.no-export)' } },
            { extend: 'csv',   exportOptions: { columns: ':not(.no-export)' } },
            { extend: 'excel', exportOptions: { columns: ':not(.no-export)' } },
            { extend: 'pdf',   exportOptions: { columns: ':not(.no-export)' } },
            'print'
        ]
    });

    // SweetAlert2 delete confirmation
    $(document).on('click', '.btn-delete', function (e) {
        e.preventDefault();
        const form = $(this).closest('form');
        Swal.fire({
            title: 'Delete this paper?',
            text: 'This action cannot be undone.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
});
</script>
@endpush
```

### 9d. File to CREATE: `resources/views/user/papers/show.blade.php`

Show paper details. For auto papers, display MCQ list. For manual, show metadata:

```blade
@extends('user.layouts.app')

@section('title', $paper->title)
@section('page-title', 'Paper Details')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('user.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('user.papers.index') }}">My Papers</a></li>
    <li class="breadcrumb-item active">{{ Str::limit($paper->title, 30) }}</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title">
                    <span class="badge badge-{{ $paper->type === 'auto' ? 'auto' : 'manual' }} mr-2">
                        {{ ucfirst($paper->type) }}
                    </span>
                    {{ $paper->title }}
                </h3>
                <span class="badge badge-{{ $paper->status }} badge-lg">{{ ucfirst($paper->status) }}</span>
            </div>
            <div class="card-body">
                <table class="table table-borderless table-sm">
                    <tr><th width="160">School / Institution</th><td>{{ $paper->school_name ?? '—' }}</td></tr>
                    <tr><th>Grade</th><td>{{ $paper->grade ?? '—' }}</td></tr>
                    <tr><th>Subject</th><td>{{ $paper->subject ?? '—' }}</td></tr>
                    <tr><th>Exam Date</th><td>{{ $paper->exam_date ? $paper->exam_date->format('d F Y') : '—' }}</td></tr>
                    <tr><th>Questions</th><td>{{ $paper->question_count }}</td></tr>
                    <tr><th>Created</th><td>{{ $paper->created_at->format('d M Y, h:i A') }}</td></tr>
                    <tr><th>Last Updated</th><td>{{ $paper->updated_at->diffForHumans() }}</td></tr>
                </table>

                @if($paper->type === 'auto' && !empty($paper->paper_data['selectedMcqs']))
                <hr>
                <h6 class="font-weight-bold mb-3">Questions in this paper:</h6>
                <ol>
                    @foreach($paper->paper_data['selectedMcqs'] as $index => $mcq)
                    <li class="mb-2">
                        <div class="font-weight-semibold">{{ $mcq['question_text'] ?? 'Question '.($index+1) }}</div>
                        @if(!empty($mcq['options']))
                        <div class="ml-3 mt-1">
                            @foreach($mcq['options'] as $opt)
                            <small class="d-block text-muted">{{ $opt['label'] }}. {{ $opt['option_text'] }}</small>
                            @endforeach
                        </div>
                        @endif
                    </li>
                    @endforeach
                </ol>
                @endif

            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card shadow-sm">
            <div class="card-header"><h3 class="card-title">Actions</h3></div>
            <div class="card-body d-flex flex-column gap-2">
                <a href="{{ route('user.papers.export', $paper->id) }}" class="btn btn-success btn-block mb-2">
                    <i class="fas fa-print mr-2"></i>Print / Export
                </a>
                <a href="{{ route('user.papers.edit', $paper->id) }}" class="btn btn-warning btn-block mb-2">
                    <i class="fas fa-edit mr-2"></i>Edit Metadata
                </a>
                <form method="POST" action="{{ route('user.papers.destroy', $paper->id) }}">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-block btn-delete">
                        <i class="fas fa-trash mr-2"></i>Delete Paper
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).on('click', '.btn-delete', function(e) {
    e.preventDefault();
    const form = $(this).closest('form');
    Swal.fire({
        title: 'Delete this paper?',
        text: 'This action cannot be undone.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        confirmButtonText: 'Delete'
    }).then(r => { if (r.isConfirmed) form.submit(); });
});
</script>
@endpush
```

### 9e. File to CREATE: `resources/views/user/papers/edit.blade.php`

```blade
@extends('user.layouts.app')

@section('title', 'Edit Paper')
@section('page-title', 'Edit Paper')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('user.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('user.papers.index') }}">My Papers</a></li>
    <li class="breadcrumb-item"><a href="{{ route('user.papers.show', $paper->id) }}">{{ Str::limit($paper->title, 25) }}</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header">
                <h3 class="card-title">Edit Paper Metadata</h3>
                <div class="card-tools">
                    <span class="badge badge-{{ $paper->type === 'auto' ? 'auto' : 'manual' }}">
                        {{ ucfirst($paper->type) }} Paper
                    </span>
                </div>
            </div>
            <form method="POST" action="{{ route('user.papers.update', $paper->id) }}">
                @csrf @method('PUT')
                <div class="card-body">

                    <div class="form-group">
                        <label>Paper Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                               value="{{ old('title', $paper->title) }}" required>
                        @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Subject</label>
                                <input type="text" name="subject" class="form-control"
                                       value="{{ old('subject', $paper->subject) }}" placeholder="e.g. Physics, Chemistry">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Grade / Class</label>
                                <input type="text" name="grade" class="form-control"
                                       value="{{ old('grade', $paper->grade) }}" placeholder="e.g. Grade 10">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>School / Institution</label>
                        <input type="text" name="school_name" class="form-control"
                               value="{{ old('school_name', $paper->school_name) }}" placeholder="School or institution name">
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Exam Date</label>
                                <input type="date" name="exam_date" class="form-control"
                                       value="{{ old('exam_date', $paper->exam_date?->format('Y-m-d')) }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Status <span class="text-danger">*</span></label>
                                <select name="status" class="form-control">
                                    <option value="draft" {{ old('status', $paper->status) === 'draft' ? 'selected' : '' }}>Draft</option>
                                    <option value="published" {{ old('status', $paper->status) === 'published' ? 'selected' : '' }}>Published</option>
                                </select>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="card-footer d-flex justify-content-between">
                    <a href="{{ route('user.papers.show', $paper->id) }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left mr-1"></i>Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i>Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
```

---

## 10. PHASE 7: SPA MODIFICATIONS

### 10a. File to MODIFY: `resources/views/app.blade.php`

Find the `<script>` block that sets `window.authUser`. After it, add:

```blade
<script>
    // Existing authUser injection stays
    window.authUser = {!! json_encode([...]) !!};

    // ADD THESE LINES:
    window.initialMode   = "{{ request('mode', '') }}";
    window.initialPaperId = "{{ request('paper_id', '') }}";
</script>
```

Also find the existing `<a href="/">` or back-to-home links in the blade file and verify they still work.

### 10b. File to MODIFY: `resources/js/App.vue`

In the `onMounted()` hook (or at component setup), add initialization from `window.initialMode`:

```javascript
// In App.vue setup() or onMounted():
import { onMounted } from 'vue'
import { useUiStore } from './stores/uiStore'
import { useAutoPaperStore } from './stores/autoPaperStore'

// Inside setup() or onMounted():
onMounted(() => {
    const mode = window.initialMode
    if (mode && ['manual', 'auto', 'auto-preview'].includes(mode)) {
        uiStore.setView(mode)
    }

    // If a paper_id is provided (for export/load), fetch and load it
    if (window.initialPaperId) {
        loadPaperFromServer(window.initialPaperId)
    }
})

async function loadPaperFromServer(paperId) {
    try {
        const response = await window.axios.get(`/api/user/papers/${paperId}`)
        const { paper, paper_data } = response.data
        if (paper.type === 'auto' && paper_data) {
            autoPaperStore.setPaperMeta({
                paperTitle: paper_data.paperTitle || paper.title,
                schoolName: paper_data.schoolName || paper.school_name,
                paperDate:  paper_data.paperDate  || paper.exam_date,
                grade:      paper_data.grade      || paper.grade,
                subject:    paper_data.subject    || paper.subject,
            })
            autoPaperStore.setSelectedMcqs(paper_data.selectedMcqs || [])
            uiStore.setView('auto-preview')
        }
        // For manual papers — load into examStore (implement based on existing projectStore pattern)
    } catch (err) {
        console.error('Failed to load paper from server:', err)
    }
}
```

### 10c. File to MODIFY: `resources/js/views/HomeScreen.vue`

Add a "← Back to Dashboard" link at the top of HomeScreen (so user can return to dashboard if they navigated directly to `/`):

```vue
<template>
    <div class="home-screen">
        <!-- Add this back-to-dashboard link at the top -->
        <div class="text-center mb-3">
            <a href="/user/dashboard" class="btn btn-sm btn-outline-secondary">
                ← Back to Dashboard
            </a>
        </div>

        <!-- Existing content unchanged -->
        ...
    </div>
</template>
```

### 10d. File to MODIFY: `resources/js/views/AutoPaperPreview.vue`

This is the most important change. Add server save functionality:

**In `<template>` — add a Save button to the action bar:**

Find the existing action bar (currently has: "Edit Selection", "Print Paper", "Home") and add:

```vue
<button @click="savePaper" :disabled="isSaving" class="btn btn-success">
    <span v-if="isSaving">
        <i class="fas fa-spinner fa-spin mr-1"></i>Saving...
    </span>
    <span v-else>
        <i class="fas fa-save mr-1"></i>
        {{ savedPaperId ? 'Saved ✓' : 'Save to My Papers' }}
    </span>
</button>
```

**In `<script setup>` — add save logic:**

```javascript
import { ref } from 'vue'
import { useAutoPaperStore } from '../stores/autoPaperStore'
import { useUiStore } from '../stores/uiStore'

const autoPaperStore = useAutoPaperStore()
const uiStore        = useUiStore()

const isSaving    = ref(false)
const savedPaperId = ref(window.initialPaperId || null)

async function savePaper() {
    isSaving.value = true
    try {
        const payload = {
            title:      autoPaperStore.paperTitle || 'Untitled Paper',
            type:       'auto',
            grade:      autoPaperStore.grade,
            subject:    autoPaperStore.subject,
            school_name: autoPaperStore.schoolName,
            exam_date:  autoPaperStore.paperDate,
            status:     'draft',
            paper_data: {
                paperTitle:   autoPaperStore.paperTitle,
                schoolName:   autoPaperStore.schoolName,
                paperDate:    autoPaperStore.paperDate,
                grade:        autoPaperStore.grade,
                subject:      autoPaperStore.subject,
                selectedMcqs: autoPaperStore.selectedMcqs,
            },
        }

        let response
        if (savedPaperId.value) {
            // Update existing paper
            response = await window.axios.put(`/api/user/papers/${savedPaperId.value}`, payload)
        } else {
            // Create new paper
            response = await window.axios.post('/api/user/papers', payload)
            savedPaperId.value = response.data.paper.id
        }

        // Show success toast (use existing uiStore toast system if available)
        showSuccessToast('Paper saved! View in your dashboard.')

    } catch (err) {
        console.error('Save failed:', err)
        showErrorToast('Failed to save paper. Please try again.')
    } finally {
        isSaving.value = false
    }
}

function goToDashboard() {
    window.location.href = '/user/dashboard'
}

// Helper toast functions — use existing toast system from uiStore
// OR implement a simple inline toast:
function showSuccessToast(msg) {
    // If uiStore has showToast:
    // uiStore.showToast({ type: 'success', message: msg })
    // Fallback:
    alert(msg)
}
function showErrorToast(msg) {
    alert(msg)
}
```

**Replace the existing "Home" button functionality** to offer both "Go to Dashboard" and the existing reset:

```vue
<button @click="goToDashboard" class="btn btn-secondary">
    <i class="fas fa-th-large mr-1"></i>My Dashboard
</button>
```

### 10e. File to MODIFY: `resources/js/components/TopBar.vue`
(Add Save button for Manual Paper mode)

Inside the TopBar, find where the existing buttons are rendered. Add a "Save to My Papers" button that's visible ONLY when `uiStore.currentView === 'manual'`:

```vue
<!-- Add in TopBar template, near other action buttons: -->
<button
    v-if="uiStore.currentView === 'manual'"
    @click="saveManualPaper"
    :disabled="isSavingManual"
    class="btn btn-sm btn-success ml-2"
    title="Save to My Papers (server)"
>
    <i class="fas fa-cloud-upload-alt mr-1"></i>
    <span v-if="isSavingManual">Saving...</span>
    <span v-else>{{ manualPaperId ? 'Saved ✓' : 'Save' }}</span>
</button>
```

In `<script setup>`:

```javascript
import { ref } from 'vue'
import { useUiStore } from '../stores/uiStore'
import { useExamStore } from '../stores/examStore'

const uiStore   = useUiStore()
const examStore = useExamStore()

const isSavingManual = ref(false)
const manualPaperId  = ref(window.initialPaperId || null)

async function saveManualPaper() {
    isSavingManual.value = true
    try {
        // Snapshot current examStore state (same format as projectStore)
        const paperSnapshot = {
            pages:    examStore.pages,
            metadata: examStore.metadata,   // adjust to actual examStore structure
            blocks:   examStore.blocks,     // adjust as needed
        }

        const payload = {
            title:      examStore.metadata?.title || examStore.paperTitle || 'Untitled Manual Paper',
            type:       'manual',
            grade:      examStore.metadata?.grade || null,
            subject:    examStore.metadata?.subject || null,
            school_name: examStore.metadata?.organization || null,
            status:     'draft',
            paper_data: paperSnapshot,
        }

        let response
        if (manualPaperId.value) {
            response = await window.axios.put(`/api/user/papers/${manualPaperId.value}`, {
                paper_data: paperSnapshot,
                title: payload.title,
            })
        } else {
            response = await window.axios.post('/api/user/papers', payload)
            manualPaperId.value = response.data.paper.id
        }

        // Show success toast via existing toast system
        uiStore.showToast?.({ type: 'success', message: 'Paper saved to your account!' })
                          // ^ adjust to match existing toast API in uiStore

    } catch (err) {
        console.error('Save manual paper failed:', err)
    } finally {
        isSavingManual.value = false
    }
}
```

**IMPORTANT**: Check the actual field names in `examStore.js` before implementing. The `examStore` field names must match exactly. Run `console.log(examStore.$state)` in browser to verify the structure.

---

## 11. IMPORTANT CONSTRAINTS & CAUTIONS

### ⚠️ Do NOT break existing admin flow
- Admin users must still go to `/` (SPA) after login — NOT to `/user/dashboard`
- Admin dashboard at `/admin/dashboard` must remain untouched
- All existing admin routes, controllers, views are READ-ONLY — only modify if explicitly stated

### ⚠️ CSRF for API calls
- The Vue SPA uses `window.axios` (configured in `bootstrap.js`) which automatically sends CSRF token via `X-XSRF-TOKEN` header from the `XSRF-TOKEN` cookie
- The API routes use `auth` middleware (session-based), NOT `auth:sanctum`
- Do NOT add `\Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class` unless already present — the SPA already works without Sanctum

### ⚠️ `routes/api.php` registration
- Per WALKTHROUGH §4a, `routes/api.php` is registered via `bootstrap/app.php` with `api:` routing
- Verify this: `bootstrap/app.php` should have `->withRouting(... api: __DIR__.'/../routes/api.php' ...)`
- New API routes added to `routes/api.php` will automatically be prefixed with `/api`

### ⚠️ examStore field names
- Before implementing `saveManualPaper()` in TopBar.vue, read `resources/js/stores/examStore.js` FULLY
- Use the EXACT field names from examStore — do NOT assume `examStore.pages` or `examStore.metadata` without verifying
- The `paper_data` JSON for manual papers should match whatever `projectStore.js` serializes to IndexedDB

### ⚠️ `uiStore.showToast()` signature
- Read `resources/js/stores/uiStore.js` to find the exact toast action signature before calling it
- If the method doesn't exist or has a different signature, implement a simple inline notification instead

### ⚠️ `app.blade.php` CSRF meta tag
- The `<meta name="csrf-token">` tag must exist in `app.blade.php` — it's required by `bootstrap.js` for Axios CSRF setup
- If it's missing, add `<meta name="csrf-token" content="{{ csrf_token() }}">` to the `<head>`

### ⚠️ AdminLTE assets for user dashboard
- All AdminLTE assets are already in `public/adminlte/` — no new downloads needed
- Use the SAME paths as admin views: `asset('adminlte/...')`
- DataTables Buttons requires JSZip (`plugins/jszip/`) and pdfmake (`plugins/pdfmake/`) for Excel/PDF export — both already exist in the admin dashboard

### ⚠️ `Api\` namespace
- The `UserPaperApiController` is in `app/Http/Controllers/Api/` directory
- Create this directory if it doesn't exist: `mkdir -p app/Http/Controllers/Api`
- Namespace: `namespace App\Http\Controllers\Api;`
- In `routes/api.php` import: `use App\Http\Controllers\Api\UserPaperApiController;`

---

## 12. EXECUTION ORDER

Run in this exact sequence:

```bash
# 1. Create the Api directory
mkdir -p app/Http/Controllers/Api

# 2. Create migration and run it
php artisan make:migration create_user_papers_table
# (replace content as specified, then:)
php artisan migrate

# 3. Create model
# (create app/Models/UserPaper.php as specified)

# 4. Create controllers
# (create UserDashboardController.php, UserPaperController.php, Api/UserPaperApiController.php)

# 5. Create blade view directories
mkdir -p resources/views/user/layouts
mkdir -p resources/views/user/papers

# 6. Create all blade views (layouts/app, dashboard, papers/index, show, edit)

# 7. Modify routes/web.php (root GET / + /spa + /user/* routes)

# 8. Modify routes/api.php (add /api/user/papers routes)

# 9. Modify AuthenticatedSessionController.php (role-based redirect)

# 10. Modify RegisteredUserController.php (redirect to user dashboard)

# 11. Modify bootstrap/app.php (role-aware redirectUsersTo)

# 12. Modify resources/views/app.blade.php (add window.initialMode + window.initialPaperId)

# 13. Modify Vue SPA files (App.vue, HomeScreen.vue, AutoPaperPreview.vue, TopBar.vue)

# 14. Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan event:clear

# 15. Rebuild frontend
npm run build
```

---

## 13. TESTING CHECKLIST

After implementation, verify:

- [ ] Guest visits `/` → sees landing page
- [ ] Admin logs in → redirected to `/` (SPA with HomeScreen)
- [ ] User logs in → redirected to `/user/dashboard` (AdminLTE page with stats)
- [ ] User dashboard sidebar: Dashboard, My Papers, Manual Paper, Auto Paper
- [ ] "Manual Paper" sidebar link → opens SPA in manual mode (canvas editor, no HomeScreen shown)
- [ ] "Auto Paper" sidebar link → opens SPA in auto mode (goes directly to AutoPaperGenerator)
- [ ] Auto paper created → "Save to My Papers" button works → paper appears in dashboard
- [ ] Manual paper in SPA → "Save" button in TopBar works → paper saved to DB
- [ ] `/user/papers` → DataTable loads with all user papers, search + export works
- [ ] Edit metadata form → saves correctly → success redirect to show page
- [ ] Delete paper → SweetAlert2 confirm → paper deleted → redirect to index
- [ ] Export button → opens SPA in preview/print mode with paper loaded
- [ ] Admin cannot access `/user/dashboard` (redirected to `/`)
- [ ] User cannot access `/admin/*` routes (403 or redirect)
- [ ] API `POST /api/user/papers` returns 201 with paper data
- [ ] API `PUT /api/user/papers/{id}` rejects wrong user's paper (403)

---

## 14. NOTES ON `autoPaperStore.js` FIELDS

Before modifying `AutoPaperPreview.vue`, read `autoPaperStore.js` and verify the EXACT field names. Based on the WALKTHROUGH documentation:

```
autoPaperStore state fields:
- paperTitle    (string)
- schoolName    (string)
- paperDate     (string)
- grade         (string)
- subject       (string)
- selectedMcqs  (array of MCQ objects from question_bank API)

autoPaperStore getter:
- totalMarks    (computed from selectedMcqs)

autoPaperStore actions:
- setPaperMeta({ paperTitle, schoolName, paperDate, grade, subject })
- setSelectedMcqs(mcqs)
- reset()
```

The `selectedMcqs` array items come from the API `/api/question-bank/filter` and have this structure:
```json
{
  "id": 1,
  "question_text": "...",
  "image_path": null,
  "subject": "Physics",
  "grade": "Grade 9",
  "topic": "...",
  "marks": 1,
  "options": [
    { "label": "A", "option_text": "...", "image_path": null },
    { "label": "B", "option_text": "...", "image_path": null },
    { "label": "C", "option_text": "...", "image_path": null },
    { "label": "D", "option_text": "...", "image_path": null }
  ]
}
```

Store this FULL object array in `paper_data.selectedMcqs` when saving — so the show page can display the full questions.
```

---

*This prompt is self-contained and production-ready. Follow phases in order, respect the IMPORTANT CONSTRAINTS section, and verify existing field names before writing Vue code.*
