# ExamCraft Pro v36 — Modern Migration Walkthrough

This document provides a comprehensive technical overview of the **ExamCraft Pro v36** project. It serves as a source of truth for understanding the architecture, feature set, and implementation details of the migration from a monolithic HTML template to a professional full-stack application.
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan event:clear

---

## 1. Project Mission
ExamCraft Pro is a professional exam paper authoring tool designed for educators. It enables the creation of high-quality examination papers with automated numbering, precise Cambridge-style typography, and professional layouts, while providing both a reactive local-first SPA experience and a robust server-side administration dashboard.

---

## 2. Architecture & Tech Stack
The project is built as a **Hybrid Full-Stack Application**:

### **Backend (Laravel 12)**
- **Role**: Serves as the API layer, authentication provider, and host for **two** server-side dashboards — the admin panel and a user-facing "My Papers" dashboard — plus a set of SPA data APIs.
- **Persistence**: MySQL (Server-side storage) + Eloquent ORM.
- **Routing**: RESTful resource controllers with nested route model binding, a role-aware root route, SPA launcher routes, and JSON API endpoints for the Vue SPA.
- **Templating**: Laravel Blade (used for Auth pages, the Admin Dashboard, and the User Dashboard).

### **Frontend (Vue 3 SPA)**
- **Role**: The core "Exam Designer" interface plus a Home Screen launcher and Auto Paper Generator wizard.
- **View Switching**: Via Pinia `uiStore.currentView` — no Vue Router. Views: `home` (launcher), `manual` (canvas editor), `auto` (paper generator wizard), `auto-preview` (print preview).
- **Architecture**: Composition API with modular components and composables.
- **State Management**: Pinia — 5 stores: examStore (paper data), uiStore (UI state + view routing), typoStore (typography presets), projectStore (IndexedDB CRUD), autoPaperStore (paper generator wizard state).
- **Persistence**: IndexedDB (via `projectStore`) for local-first, offline-capable project management, plus server-side "My Papers" persistence (via the `/api/user/*` endpoints + Blade dashboard).
- **Build Tool**: Vite (handles HMR, asset bundling, and CSS processing).

### **Key Dependencies**
- **UI**: Bootstrap 5 (SPA), Bootstrap 4 (AdminLTE Dashboard), Font Awesome 6.5.
- **Interactions**: SortableJS (Drag & Drop), @vueuse/core (Debounced saving).
- **Export**: jsPDF, html2canvas (PDF generation), QRCode.js.
- **HTTP**: Axios (window.axios — globally available from bootstrap.js).

---

## 3. Full Project Directory Structure

```text
examcraft-pro/
├── app/
│   ├── Models/                              → 12 Eloquent models (User, Role, ExamPaper, Page, UserPaper, …)
│   │   ├── User.php                         → HasOne(Role), HasMany(ExamPaper, QuestionBank, UserPaper); isAdmin() helper
│   │   ├── Role.php                         → Dedicated roles table (admin | user)
│   │   ├── ExamPaper.php
│   │   ├── Page.php
│   │   ├── Block.php
│   │   ├── McqBlock.php
│   │   ├── McqOption.php
│   │   ├── ExamPaperTopic.php
│   │   ├── QuestionBank.php                 → JSON `data` column (stem_text, stem_image, options[], correct_answer)
│   │   ├── QuestionBankOption.php           → Retained model for historical reference (table dropped by JSON migration)
│   │   ├── UserPaper.php                    → Server-persisted user paper; `paper_data` JSON cast + question_count accessor
│   │   └── ProjectSnapshot.php
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Auth/                            → Breeze v2.4.2 authentication controllers (10 files)
│   │   │   │   ├── AuthenticatedSessionController.php  → Login/logout session handling
│   │   │   │   ├── RegisteredUserController.php       → Registration
│   │   │   │   ├── PasswordResetLinkController.php    → Forgot password email
│   │   │   │   ├── NewPasswordController.php          → Reset password form
│   │   │   │   ├── EmailVerificationPromptController.php → Verify email notice
│   │   │   │   ├── EmailVerificationNotificationController.php → Resend verification
│   │   │   │   ├── VerifyEmailController.php          → Verify email link handler
│   │   │   │   ├── ConfirmablePasswordController.php  → Confirm password gate
│   │   │   │   ├── PasswordController.php             → Update password
│   │   │   │   ├── LoginController.php                → (legacy — pre-Breeze)
│   │   │   │   └── RegisterController.php             → (legacy — pre-Breeze)
│   │   │   ├── AdminController.php            → Admin dashboard with stats aggregation
│   │   │   ├── ProfileController.php           → Breeze profile edit/update/delete
│   │   │   ├── ExamCraftController.php         → Returns app.blade.php view serving the SPA shell
│   │   │   ├── UserController.php              → Admin-side CRUD for users and role management
│   │   │   ├── ExamPaperController.php         → Full CRUD for exam papers (admin & API)
│   │   │   ├── PageController.php              → Nested CRUD (exam-papers.pages)
│   │   │   ├── BlockController.php             → Nested CRUD (exam-papers.pages.blocks)
│   │   │   ├── McqBlockController.php          → Full CRUD for MCQ-specific block data
│   │   │   ├── McqOptionController.php         → Nested CRUD (mcq-blocks.mcq-options)
│   │   │   ├── ExamPaperTopicController.php    → Nested CRUD (exam-papers.topics)
│   │   │   ├── QuestionBankController.php      → Full CRUD for question bank + filter() API method (grade+subject query); stores stem/options/correct-answer in the JSON `data` column
│   │   │   ├── QuestionBankOptionController.php → Nested CRUD (question-bank.options) — retained but now unused (options live in JSON)
│   │   │   ├── UserDashboardController.php      → User-scoped dashboard with paper stats (total/auto/manual/published + recent)
│   │   │   ├── UserPaperController.php          → Blade CRUD for a user's papers (index/show/edit/update/destroy/export) — non-admin only
│   │   │   ├── UserPaperApiController.php       → JSON API for the SPA (index/store/show/update) under session auth
│   │   │   └── Controller.php                  → Base controller
│   │   └── Middleware/
│   │       └── AdminMiddleware.php           → RBAC check for admin access (via $user->isAdmin())
│   └── Providers/
├── bootstrap/
│   └── app.php                               → Middleware & routing registration
├── config/                                   → Laravel system configuration
├── database/
│   └── migrations/                           → 20 migrations defining the examcraft schema (incl. user_papers + question-bank JSON conversion)
├── public/
│   ├── adminlte/                             → Centralized AdminLTE v3.2.0 assets (dist, plugins)
│   ├── css/
│   │   └── landing.css                       → Standalone marketing landing page styles (960+ lines)
│   └── storage/                              → Symlink to storage/app/public/ (images)
├── resources/
│   ├── css/
│   │   └── app.css                           → Theme system (1,700+ lines) + Tailwind directives. 4 themes (Day/Afternoon/Night/Late Night) with CSS custom properties, paper typography variables, all component styles (topbar, panels, canvas, blocks, rulers, modals, toasts, etc.), and the A4 @page + @media print rules for auto-paper export
│   ├── js/
│   │   ├── app.js                            → Vue app entry point; initializes Pinia
│   │   ├── App.vue                           → Root component; handles layout & auto-save
│   │   ├── stores/
│   │   │   ├── examStore.js                   → Paper metadata, blocks, pages, styling, selected block
│   │   │   ├── uiStore.js                     → Zoom, panel widths, active tab, theme, toasts, currentView (SPA view routing)
│   │   │   ├── typoStore.js                   → Typography settings + 6 Cambridge presets
│   │   │   ├── projectStore.js                → IndexedDB CRUD, import/export
│   │   │   └── autoPaperStore.js              → Auto Paper Generator wizard state (paperTitle, paperCode, session, duration, grade, subject, additionalMaterials, instructions, logo, selectedMcqs, totalMarks); auto-persists wizard state to localStorage so a refresh keeps the selection
│   │   ├── composables/                      → Reusable logic (useZoom, useTypography, etc.)
│   │   ├── components/                       → UI components (Canvas, Panels, Blocks)
│   │   └── views/                            → SPA top-level views (HomeScreen, AutoPaperGenerator, AutoPaperPreview)
│   └── views/
│       ├── admin/                            → Blade templates for Admin Dashboard
│       │   ├── users/                        → User management views (index, create, edit, show)
│       │   ├── questions/                    → Question bank views (index, create, show, edit) — now read/write the JSON `data` column
│       │   ├── papers/                      → Exam papers views (index, create, show, edit)
│       │   └── dashboard.blade.php           → Dashboard landing page
│       ├── user/                             → Blade templates for the User Dashboard (AdminLTE)
│       │   ├── dashboard.blade.php           → Stats cards + quick actions + recent papers
│       │   ├── layouts/app.blade.php         → Shared AdminLTE layout (sidebar, navbar, toastr)
│       │   └── papers/                       → My Papers views (index, show, edit)
│       ├── auth/                             → Blade templates for Login/Register (Breeze v2.4.2)
│       │   ├── combined.blade.php              → Flip-card Login/Register (standalone full-page; the active view)
│       │   ├── login.blade.php                → Login form (legacy — kept as backup)
│       │   ├── register.blade.php             → Registration form (legacy — kept as backup)
│       │   ├── forgot-password.blade.php       → Password reset request (standalone full-page redesign)
│       │   ├── reset-password.blade.php        → New password form
│       │   ├── verify-email.blade.php          → Email verification notice
│       │   └── confirm-password.blade.php      → Password confirmation gate
│       ├── dashboard.blade.php                → Breeze authenticated dashboard
│       ├── app.blade.php                     → SPA Shell (injects window.authUser with nullsafe role)
│       └── landing.blade.php                 → Marketing landing page (guests only)
├── routes/
│   ├── web.php                               → Role-aware root GET / (guest→landing, admin→admin.dashboard, user→user.dashboard), SPA launchers (/user/manual, /user/auto), profile routes, Admin panel (auth+admin), /api/question-bank/filter, /api/user/* paper APIs, User dashboard + papers CRUD, SPA catch-all
│   ├── api.php                               → Registered in bootstrap/app.php (api: routing); intentionally empty — the SPA "API" endpoints live in web.php for session/CSRF auth
│   ├── auth.php                              → Breeze v2.4.2 authentication routes (login, register, password reset, email verification, confirm-password, logout)
│   └── console.php                           → Artisan console commands routing
├── storage/app/public/
│   ├── questions/                            → Uploaded question images
│   └── options/                              → Uploaded MCQ option images
├── vite.config.js                            → Vite configuration: Laravel plugin + Vue 3 SFC compiler (transformAssetUrls), Vue esm-bundler alias, CORS enabled, host binding to 127.0.0.1
└── package.json                              → Frontend dependencies & scripts
```

---

## 4. The Block System (Core Designer)
The SPA uses a modular "Block" system where each part of an exam paper is a distinct component.

### **Supported Block Types**
1.  **MCQ (Multiple Choice)**:
    - **Logic**: Auto-renumbering based on document position.
    - **Options**: Supports 4 mandatory options (A-D) and up to 2 dynamic options (E-F).
    - **Layouts**: 1-column, 2-column, or inline.
    - **Rich Content**: Supports images (above/below stem) and "Answer Boxes" toggle.
2.  **Section**: Headers and sub-headers with optional divider lines.
3.  **Text**: Rich-text instructions or context blocks with font-size and alignment controls.
4.  **Image**: Independent image blocks with captions and width scaling.
5.  **Table**: Dynamic row/column management with specialized variants for exam data.
6.  **Divider**: Horizontal separators with customizable styles (solid/dashed/dotted).

---
## 4a. Auto Paper Generator (New — August 11, 2026; extended August 16–17, 2026)

A wizard-based paper generation flow that sources MCQs from the server-side question bank and produces a Cambridge-styled MCQ paper.

### View Flow
```
Login → User Dashboard ┬→ "Manual Paper" (SPA launcher) → canvas editor
                       └→ "Auto Paper" (SPA launcher) → AutoPaperGenerator
                            → AutoPaperPreview → Save / Print
```

### Backend
- **`routes/api.php`**: Created (did not exist before). Registered in `bootstrap/app.php` via `api:` routing. Intentionally **empty** — the SPA "API" endpoints are defined in `routes/web.php` (not `api.php`) because they use Laravel's session cookie + CSRF auth rather than token auth.
- **`GET /api/question-bank/filter?grade=X&subject=Y`**: Returns questions matching grade+subject, reading the stem/options/correct-answer from the JSON `data` column and mapping option images through `Storage::url()`.
- **Migration `2026_08_11_000001_add_grade_to_question_bank_table.php`**: Added `grade` column (nullable string) after `subject` on `question_bank` table.
- **`QuestionBank.php` model**: `'grade'` added to `$fillable`.

### Frontend Stores
- **`uiStore.js`** — `currentView: 'home'` state + `setView(view)` action (drives all view switching in App.vue).
- **`autoPaperStore.js`** — Composition API Pinia store. State: `paperTitle`, `schoolName`, `paperDate`, `grade`, `subject`, `paperCode`, `session`, `duration`, `additionalMaterials`, `instructions`, `logoFile`, `logoDataUrl`, `selectedMcqs`. Getter: `totalMarks` (question count). Actions: `setPaperMeta()`, `setLogo()`, `setSelectedMcqs()`, `reset()`. Ships `DEFAULT_INSTRUCTIONS` and `DEFAULT_ADDITIONAL_MATERIALS` constants for the Cambridge answer-sheet boilerplate.

### View Components (`resources/js/views/`)
| Component | View ID | Purpose |
|-----------|---------|---------|
| `HomeScreen.vue` | `home` | Two cards: "Create Manual Paper" (→ `manual`) and "Auto Paper Generator" (→ `auto`). Non-admin users also see a "Back to My Dashboard" link. |
| `AutoPaperGenerator.vue` | `auto` | Full paper settings form — identity (title, paper code, session, duration), institution (school, date), setup (grade → subject cascading dropdowns from a hardcoded mapping), additional materials, editable instructions, and an optional logo upload. "Load MCQs" fetches `/api/question-bank/filter`. Checkbox MCQ list with select-all/deselect-all. "Generate Paper" stores the selection and navigates to `auto-preview`. Also supports **editing** an existing paper via `?paper_id=N` (see below). Form fields hydrate from the persisted store and re-check the previous MCQ selection after a refresh; logo uploads are downscaled to a max 300px JPEG before saving. |
| `AutoPaperPreview.vue` | `auto-preview` | Read-only A4 paper sheet (210mm×297mm) rendering the Cambridge header (logo + school, subject/code/session/duration, additional materials, bolded-keyword instructions, footer note) and Section A questions with A/B/C/D options and images. All question stems and option text are rendered as sanitized HTML via `cleanText()` (strips `<a>` tags to plain text, removes inline `style` attributes) to prevent theme bleed-through and link artifacts. Option rows put the bold A–D label left, text center, and a readable image (≤75px × 110px) right; row height is content-driven (3px padding, 6px gaps). Forced `color: #000 !important` on stems and options ensures print fidelity. Action bar: "Edit Selection" (→ `auto`), **"Save to My Papers"** (POST/PUT `/api/user/papers`), "Print Paper" (`window.print()`), "Home". Print output is driven by the global A4 `@media print` rules in `app.css`. |

### Auto-paper Persistence (server-side "My Papers")
- Both the **manual** editor (TopBar "Save to My Papers" cloud button) and the **auto** preview ("Save to My Papers") persist the paper to the server via `POST /api/user/papers` (create) or `PUT /api/user/papers/{id}` (update) using `window.axios`.
- The manual paper payload bundles the full designer state (`pages`, `paperMeta`, `typoState`, `styleState`, `globalOpts`, `coverFooter`, `pageFooter`) into the `paper_data` JSON. The auto paper payload stores its wizard state (`paperTitle`, `schoolName`, `paperCode`, `session`, `duration`, `additionalMaterials`, `instructions`, `logoDataUrl`, `selectedMcqs`).
- After the first save, the returned paper `id` is retained (in `manualPaperId` / `savedPaperId`) so subsequent saves **update** rather than duplicate.
- **Editing a saved paper**: the `/user/papers/{id}/edit` route renders `AutoPaperGenerator.vue` with `?paper_id=N`; `loadPaperForEdit()` fetches `/api/user/papers/{id}`, mirrors the `paper_data` into the store + local form refs, re-selects grade/subject, and re-loads the matching MCQs with prior selections restored. Manual papers instead reopen the SPA in `manual` mode via `UserPaperController::export()`.

### SPA Launchers & Role-Aware Entry
- The root `/` route is now **role-aware**: guests → `landing`, admins → `admin.dashboard`, regular users → `user.dashboard`.
- Two clean launcher URLs drive the SPA without query strings: `GET /user/manual` and `GET /user/auto`, both `auth`-protected and passing `initialMode` into `app.blade.php`.
- `app.blade.php` injects `window.initialMode` and `window.initialPaperId`, consumed by `App.vue`'s `initFromLauncher()`. For manual papers it restores pages/typography/global opts; for auto papers it hydrates the store and shows `auto-preview`.
- `bootstrap/app.php` now uses a closure for `redirectUsersTo()` returning `/admin/dashboard` or `/user/dashboard` based on `isAdmin()`, and `bootstrap.js` injects the CSRF token into `window.axios` default headers for same-origin POST/PUT/DELETE.

### UI Updates
- **`TopBar.vue`**: Added "Back to Home" button (left-arrow, first item) — `goHome()` navigates admins to the SPA `home` view and non-admins to `/user/dashboard`. Added the "Save to My Papers" cloud-upload button wired to the `/api/user/papers` endpoint.
- **`App.vue`**: Canvas editor layout wrapped in `<template v-if="uiStore.currentView === 'manual'">`; three new views added via `v-else-if`. New imports: `HomeScreen`, `AutoPaperGenerator`, `AutoPaperPreview`.

### Auto Paper Preview, Print & Wizard Persistence (August 17, 2026)

Final polish pass on the auto-paper flow — refresh-proof persistence, readable option images, and a true A4 print/PDF export.

#### Wizard state survives page refreshes
- **`autoPaperStore.js`** now persists the entire wizard state (`paperTitle`, `schoolName`, `paperDate`, `grade`, `subject`, `paperCode`, `session`, `duration`, `additionalMaterials`, `instructions`, `logoDataUrl`, `selectedMcqs`) to `localStorage` under the key `examcraft.autoPaper`. A deep `watch` writes on every change and the store rehydrates from it on load, so a refresh keeps the wizard exactly as the user left it.
- On refresh mid-wizard, `AutoPaperGenerator.vue` re-fetches the matching MCQs (`/api/question-bank/filter`) and re-checks the previously selected questions instead of starting from scratch.

#### Logo uploads are downscaled before saving
- New `resizeLogo()` in the generator resizes an uploaded logo to a max of 300px on the longest edge and re-encodes it as a JPEG data URL (PNG fallback).
- `AutoPaperPreview.vue` applies the same `shrinkLogoDataUrl()` transform on save, so previously saved full-resolution PNG logos are compressed on their next save too. This stops `paper_data` from ballooning past MySQL's `max_allowed_packet` — the cause of intermittent 500 errors when saving.

#### Print/PDF export overhaul
- Print styles moved out of the component into a global `@media print` block at the end of `resources/css/app.css`, with an A4 `@page` setup. The `#app` wrapper previously carried `overflow: hidden; height: 100vh` (needed for the canvas editor) which clipped the print-out to roughly one screen; the print rules reset `html/body/#app`, hide all SPA chrome (topbar, action bar, nav, sidebar), and keep the paper sheet at 210mm × 297mm with proper margins.
- Questions never split mid-question in print (`page-break-inside: avoid` / `break-inside: avoid` on `.ap-q-item`).
- Print-specific option row rules (`.ap-q-opt` / `.ap-q-opt--with-img`) enforce compact padding (3px 0) and 6px gaps between rows. Option images (`.ap-q-opt-img`) are capped at 75px × 110px with `object-fit: contain` and forced `display: inline-block; visibility: visible`. Stem images (`.ap-q-stem-img`) are capped at 80px × 100px with `object-fit: contain`.

#### Preview paper layout (Cambridge conventions)
- The sheet is a true A4 page (210mm × 297mm) at 10pt serif type. The header stack: logo + school name (row 1), divider rule, subject/paper code/session/duration right-aligned (row 3), "Additional Materials:" label + list (row 4), "READ THESE INSTRUCTIONS FIRST" centered heading (row 6), instructions body with bolded key phrases (row 7), divider, and a footer note that now reads "Answer all N questions." — the fabricated "This document consists of N printed pages" counter was removed.
- Question stems and option text render as sanitized HTML via `cleanText()` (links replaced with plain text, inline `style` attributes stripped). This function is applied to **all three** content areas — instructions, question stems, and option text — ensuring no theme bleed-through, no clickable links, and no rogue inline styles in the printed output. The instructions auto-replace the hardcoded word "forty" with the actual number of selected questions.
- **Option rows** follow the Cambridge image-option layout: bold A–D label on the left (fixed 18px column), option text in the middle filling the remaining space, and the image on the right capped at 75px × 110px so graphs, diagrams and circuits stay readable. Row height is driven by content only (the image sets it), with 3px top/bottom padding and 6px gaps between rows — no artificial inflation. Both `.ap-q-opt` and `.ap-q-opt--with-img` share a unified flex layout with `!important` overrides to prevent theme CSS from inflating rows.
- **Print fidelity**: forced `color: #000 !important` and `text-decoration: none !important` on `.ap-q-stem`, `.ap-q-opt`, and all their descendants ensures no dark-theme colors or underlines leak into the printed page. The instructions heading uses `text-decoration: none` (not `underline`) per Cambridge convention.
- **Materials section**: changed from `<div>`-based layout to semantic `<span>/<ul>/<li>` markup (`ap-materials-row` / `ap-materials-list`) with flex alignment and `list-style: none`.
- **Question numbering**: changed from `<b>{{ idx + 1 }}</b>&nbsp;` to `<span class="ap-q-number">{{ idx + 1 }}</span>` with a dedicated `.ap-q-number` class (`font-weight: bold; margin-right: 4px`) for consistent spacing.

#### Home screen
- **`HomeScreen.vue`** — the "Back to My Dashboard" link now uses a `computed` `isUser` flag instead of referencing `window.authUser` directly in the template (which never resolves in Vue's render scope), so regular users reliably see the dashboard link.

---

## 5. Typography & Layout Engine
To achieve pixel-perfect "Cambridge-style" formatting, the app uses a dynamic CSS variable injection system.

### **Typography presets (`typoStore.js`)**
- **Presets**: O Level, A Level, IGCSE, Primary, Secondary, and Custom.
- **CSS Variable Injection**: The `useTypography` composable injects 13+ variables into the `:root` element:
    - `--paper-font-family`: Standardized exam fonts.
    - `--paper-q-font-size`: Question stem size (default 11pt).
    - `--paper-opt-font-size`: Options size (default 10pt).
    - `--paper-line-height`: Optimized for legibility.

---

## 6. Persistence Strategy
### **Local Storage (IndexedDB)**
- Managed via `projectStore.js`.
- Stores entire project snapshots (JSON) locally to bypass browser storage limits.
- **Auto-Save**: Uses `@vueuse/core`'s `useDebounceFn` to save the project state to IndexedDB 2 seconds after the last change.

### **Server Persistence (MySQL)**
- Used for user accounts, global question banks, and server-side exam paper management.
- **User Papers (`user_papers` table)** — the "My Papers" feature persists a user's finished papers server-side. Schema: `user_id` (FK cascade), `title`, `type` (enum `manual`|`auto`), `grade`, `subject`, `school_name`, `exam_date`, `paper_data` (longText JSON), `status` (enum `draft`|`published`). The `UserPaper` model casts `paper_data` to an array and exposes a `question_count` accessor (auto = count of `selectedMcqs`, manual = count of MCQ blocks).
- **Question Bank (`question_bank` table)** — after the JSON conversion (see §7a), each question stores its stem and options inside a single JSON `data` column rather than a separate options table.

---

## 7. Admin Dashboard (AdminLTE Integration)
A secondary interface built with **AdminLTE v3.2.0** (Bootstrap 4) for high-level management.

### **Key Modules**
1.  **Dashboard**: Overview metrics (Total papers, questions, users) using Info-Box components.
2.  **User Management**:
    *   **DataTables List**: Searchable, paginated list of all users with role badges.
    *   **CRUD Operations**: Admin-only ability to add new users, edit profiles, and delete accounts (excluding self).
    *   **Role Assignment**: Toggle between `admin` and `user` roles via the `roles` relationship.
3.  **Question Bank**:
    *   **Data model**: After the JSON conversion (§7a), each question stores `subject`, `grade`, `marks`, and a JSON `data` column holding `{ stem_text, stem_image, options[{label, text, image}], correct_answer }`. Stem/option images are uploaded to `storage/app/public/questions/` and `storage/app/public/options/`.
    *   **Index (DataTables List)**: Searchable, paginated table showing all questions with columns for grade, question (stem text from JSON), subject, marks, and type. Export buttons (Copy, CSV, Excel, PDF, Print) with SweetAlert2 delete confirmations (`resources/views/admin/questions/index.blade.php`).
    *   **Create**: Form capturing grade (cascading subject dropdown), subject, marks, stem text (required unless a stem image is provided), stem image upload, options A–D (each text-required-unless-image, with image upload), and the correct-answer selector. `QuestionBankController@store` validates and builds the `data` JSON, storing the question in one request (`resources/views/admin/questions/create.blade.php`).
    *   **Show (detail view)**: Displays stem text, stem image, all options in a table with correct-answer highlighting, and metadata (grade, subject, marks, correct answer) (`resources/views/admin/questions/show.blade.php`).
    *   **Edit**: Form to update the stem, options, metadata, and replace images with live preview — preserving existing images when no new file is uploaded (`resources/views/admin/questions/edit.blade.php`).
    *   The legacy standalone **Manage Options** page and the separate `question_bank_options` table were removed in the JSON conversion (§7a). `QuestionBankOptionController`, the `QuestionBankOption` model, and the nested `question-bank.options` resource routes are retained for reference but are no longer used by the active flow.
4.  **Exam Papers**:
    *   **Index (DataTables List)**: Searchable, paginated table of all exam papers with title, subject, exam code, year, and status columns. Export support and SweetAlert2 delete confirmations (`resources/views/admin/papers/index.blade.php`).
    *   **Create**: Form capturing all exam paper metadata — title, subject, exam code, organization, session, year, duration, typography preset, instructions, and materials (`resources/views/admin/papers/create.blade.php`).
    *   **Show (detail view)**: Displays all paper metadata including status badge, pages overview with block counts per page, and related topics/snapshots (`resources/views/admin/papers/show.blade.php`).
    *   **Edit**: Form to update all paper metadata including the status toggle (Draft / Published) (`resources/views/admin/papers/edit.blade.php`).
5.  **DataTables**: Integrated into `index.blade.php` for questions, papers, and dashboard tables, providing client-side search, sort, and export (CSV/Excel/PDF/Print) via the DataTables Buttons plugin. Powered by AdminLTE's bundled plugins (JSZip, pdfmake).

---

## 7a. Question Bank JSON Conversion (August 16, 2026)

The question bank was restructured from a relational model (a `question_bank` row plus a separate `question_bank_options` table) to a **single JSON column** on `question_bank`.

### Migration `2026_08_17_000001_convert_question_bank_to_json.php`
Runs `up()` in four steps:
1. Adds a nullable `data` **JSON column** after `grade` on `question_bank`.
2. Migrates existing rows: for each question, reads its old `question_text` column and any `question_bank_options` rows, and builds `{ stem_text, stem_image, options[], correct_answer }`.
3. Drops the `question_bank_options` table.
4. Drops the now-redundant `question_text` column.

The `buildOptions()` helper handles **both** historical layouts — the row-per-option layout (`label` + `option_text` + `option_image_url`) and the wide-row layout (a single row with `option_a_text` … `option_f_image`), detecting the layout via `hasWideColumns()`.

### Model & Controller Changes
- **`QuestionBank.php`** — `$fillable` is now `['id', 'user_id', 'subject', 'grade', 'marks', 'data']` with `data` cast to an array. The `topic`/`difficulty`/`option_type`/`stem`/`correct_answer` scalar columns from the original `question_bank` schema are no longer populated.
- **`QuestionBankController.php`** — `store()`/`update()` validate stem + options A–D + `correct_answer` and build the `data` JSON via `buildData()`/`buildOptions()`, uploading stem/option images to the `public` disk. `filter()` now reads the `data` array and reshapes options for the SPA (including `Storage::url()` for images).
- The admin `questions/*` Blade views were rewritten to read/write the JSON `data` column instead of the old relational columns.

---

## 7b. User Dashboard & "My Papers" (August 16, 2026)

A second, non-admin **User Dashboard** (AdminLTE) was added so regular users can manage their own saved papers server-side. Admins are redirected away (`UserDashboardController` and `UserPaperController` constructors bounce `isAdmin()` users back to `/`).

### Routes (`routes/web.php`, `user.` name prefix, all `auth`)
| Method | URI | Controller@method | Name |
|--------|-----|-------------------|------|
| GET | `/user/dashboard` | UserDashboardController@index | `user.dashboard` |
| GET | `/user/papers` | UserPaperController@index | `user.papers.index` |
| GET | `/user/papers/{id}` | UserPaperController@show | `user.papers.show` |
| GET | `/user/papers/{id}/edit` | UserPaperController@edit | `user.papers.edit` |
| PUT | `/user/papers/{id}` | UserPaperController@update | `user.papers.update` |
| DELETE | `/user/papers/{id}` | UserPaperController@destroy | `user.papers.destroy` |
| GET | `/user/papers/{id}/export` | UserPaperController@export | `user.papers.export` |
| GET | `/user/manual` | (closure) → `view('app', ['initialMode' => 'manual'])` | `user.manual` |
| GET | `/user/auto` | (closure) → `view('app', ['initialMode' => 'auto'])` | `user.auto` |

### SPA JSON API (`routes/web.php`, `api.user.` name prefix, session auth + CSRF)
| Method | URI | Controller@method |
|--------|-----|-------------------|
| GET | `/api/user/papers` | UserPaperApiController@index |
| POST | `/api/user/papers` | UserPaperApiController@store |
| GET | `/api/user/papers/{id}` | UserPaperApiController@show |
| PUT | `/api/user/papers/{id}` | UserPaperApiController@update |

Every `UserPaper` query is scoped to the authenticated user (`forUser(Auth::id())`), so users can only touch their own papers.

### Views (`resources/views/user/`)
- **`dashboard.blade.php`** — four info-box stats (Total / Auto / Manual / Published) plus "Create Manual Paper" and "Auto Paper Generator" quick-action cards and a "Recent Papers" table.
- **`papers/index.blade.php`** — DataTables list of the user's papers with type/grade/subject/school/status columns, export buttons, and SweetAlert2 delete confirmation.
- **`papers/show.blade.php`** — For **auto** papers, renders a full printable A4 sheet from `paper_data` (logo, school, subject/code/session, materials, instructions, Section A questions). For **manual** papers, shows a metadata table with `question_count` and export/edit/delete actions.
- **`papers/edit.blade.php`** — Edits common metadata (title, status, exam date, subject, grade, school) and, for auto papers, the paper identity fields (paper code, session, duration, materials, instructions) with a read-only question list.
- **`layouts/app.blade.php`** — shared AdminLTE layout (sidebar with Dashboard / My Papers / Manual Paper / Auto Paper nav, navbar with profile + logout, toastr flash messages).

---

## 8. Authentication & Authorization

### Authentication Provider — Laravel Breeze v2.4.2
Authentication is handled by **Laravel Breeze v2.4.2** (installed August 10, 2026), replacing the old custom `AuthController.php`:

```bash
composer require laravel/breeze --dev
php artisan breeze:install blade
```

### Post-Breeze Integration Fixes (August 11, 2026)

Installing Breeze caused several regressions that were resolved:

#### 8.1 CSS Wipeout — Blank White Page
Breeze replaced `resources/css/app.css` (1,673 lines) with 3 lines of Tailwind directives (`@tailwind base; @tailwind components; @tailwind utilities;`). This wiped the entire ExamCraft theme system:

- **4 theme definitions** (Day, Afternoon, Night, Late Night) — each with ~20 CSS custom properties
- **Shared CSS variables** (`--font-body`, `--font-display`, `--font-mono`, `--radius`, `--radius-sm`, `--transition`)
- **Paper typography variables** (`--paper-font-family`, `--paper-q-font-size`, etc.)
- **~1,200 lines of component styles**: topbar, panels, canvas, blocks, rulers, modals, toasts, scrollbar, form inputs, typography controls, answer key

**Root Cause**: The Vue SPA does not use Tailwind classes — every component references the custom CSS properties. Without them, all UI elements rendered with `transparent` backgrounds and `inherit` text colors (matching the dark body background), making everything invisible despite the JS running correctly.

**Fix**: Restored the full theme CSS into `resources/css/app.css` while keeping the 3 Tailwind directives at the top (these do no harm in the SPA context and preserve the Tailwind setup inherited from Breeze).

#### 8.2 Login Redirect — `/dashboard` vs role-aware home
Breeze defaults to redirecting authenticated users to `route('dashboard')` (`/dashboard`). This was replaced with a **role-aware** redirect:

1. **`AuthenticatedSessionController.php`** — `redirect()->intended()` target changed from `route('dashboard', absolute: false)` to a role check: `auth()->user()->isAdmin() ? route('admin.dashboard') : route('user.dashboard')`
2. **`bootstrap/app.php`** — `$middleware->redirectUsersTo()` is now a closure returning `route('admin.dashboard')` or `route('user.dashboard')` based on `isAdmin()`, so Breeze's auth middleware routes authenticated guests correctly
3. **`routes/web.php`** — `/dashboard` route changed from showing `view('dashboard')` to a simple `redirect('/')` with `auth` middleware only (removed `verified`)

#### 8.3 `window.authUser` Nullsafe Crash
`resources/views/app.blade.php` injects `window.authUser` with user data. The original code used `auth()->user()->role->name ?? 'user'` — but if a user has no role row in the database, `->role` returns `null`, and accessing `->name` on `null` throws a PHP error (**Attempt to read property "name" on null**) before the `??` operator can catch it. This killed the blade template mid-render, so `window.authUser` was never injected, and the SPA loaded with `undefined` user data.

**Fix**: Changed to `auth()->user()->role?->name ?? 'user'` (nullsafe operator).

#### 8.4 Avatar Dropdown Not Visible
The user avatar dropdown in `TopBar.vue` was invisible because it was clipped by two layers:

1. **`#app`** (`App.vue`) has `overflow: hidden; height: 100vh` — clips all content at the viewport boundary
2. **CSS spec hard rule** — when `overflow-x: auto` is set, browsers force `overflow-y` to `auto` regardless of the declared value (cannot have one axis scrollable and the other visible)

**Fix**: Used Vue `<Teleport to="body">` to render the dropdown menu as a direct child of `<body>`, outside the `#app` DOM tree. The dropdown position is calculated dynamically via `getBoundingClientRect()` on the avatar wrapper, stored in a reactive `dropdownStyle` object, and applied as `position: fixed` with computed `top`/`right` values. The outside-click handler was updated to check `event.target.closest('.user-dropdown-menu')` since the dropdown is no longer inside `userDropdownRef`.

### Breeze Controllers (`app/Http/Controllers/Auth/`)
9 generated controllers handle all auth flows:

| Controller | Purpose |
|------------|---------|
| `AuthenticatedSessionController.php` | Login form display + authenticate + logout (redirect target patched to `/`) |
| `RegisteredUserController.php` | Registration form + create user |
| `PasswordResetLinkController.php` | "Forgot password" form + send reset email |
| `NewPasswordController.php` | Reset password form (after email link) + update password |
| `EmailVerificationPromptController.php` | "Verify your email" notice page |
| `EmailVerificationNotificationController.php` | Resend verification email |
| `VerifyEmailController.php` | Handle signed verification link |
| `ConfirmablePasswordController.php` | Confirm password gate (before sensitive actions) |
| `PasswordController.php` | Update password for authenticated user |

Two legacy controllers (`LoginController.php`, `RegisterController.php`) remain in the directory but are no longer used by the route system — they are preserved for reference only.

### Breeze Auth Views (`resources/views/auth/`)
| View | Purpose |
|------|---------|
| `combined.blade.php` | Flip-card Login/Register — the active auth view (see §8.5) |
| `login.blade.php` | Login form (legacy — kept as backup, not routed) |
| `register.blade.php` | Registration form (legacy — kept as backup, not routed) |
| `forgot-password.blade.php` | Password reset request (standalone full-page redesign, see §8.6) |
| `reset-password.blade.php` | New password form |
| `verify-email.blade.php` | Email verification prompt |
| `confirm-password.blade.php` | Confirm password gate |

### Auth Routes (`routes/auth.php`)
All Breeze auth routes live in a separate file required at the bottom of `routes/web.php`:

| Method | URI | Controller | Name |
|--------|-----|------------|------|
| GET | `/register` | RegisteredUserController@create | `register` |
| POST | `/register` | RegisteredUserController@store | — |
| GET | `/login` | AuthenticatedSessionController@create | `login` |
| POST | `/login` | AuthenticatedSessionController@store | — |
| POST | `/logout` | AuthenticatedSessionController@destroy | `logout` |
| GET | `/forgot-password` | PasswordResetLinkController@create | `password.request` |
| POST | `/forgot-password` | PasswordResetLinkController@store | `password.email` |
| GET | `/reset-password/{token}` | NewPasswordController@create | `password.reset` |
| POST | `/reset-password` | NewPasswordController@store | `password.store` |
| GET | `/verify-email` | EmailVerificationPromptController | `verification.notice` |
| GET | `/verify-email/{id}/{hash}` | VerifyEmailController | `verification.verify` |
| POST | `/email/verification-notification` | EmailVerificationNotificationController@store | `verification.send` |
| GET | `/confirm-password` | ConfirmablePasswordController@show | `password.confirm` |
| POST | `/confirm-password` | ConfirmablePasswordController@store | — |
| PUT | `/password` | PasswordController@update | `password.update` |

### Other Breeze Additions
- **`ProfileController.php`** — handles profile edit (`profile.edit`), update (`profile.update`), and account deletion (`profile.destroy`) under `auth` middleware. Views live in `resources/views/profile/`.
- **`dashboard.blade.php`** — the Breeze default authenticated dashboard is no longer used; `/dashboard` now redirects to `/` (the SPA).

### 8.5 Flip-Card Auth Redesign (August 13, 2026)

The default Breeze login/register forms were replaced with a single **flip-card** experience matching the landing page design system.

- **New view** `resources/views/auth/combined.blade.php` — a standalone full-page Blade view (owns its own `<!DOCTYPE html>`…`</html>`, no layout extension) containing **both** the login and register forms in one 3D flip-card. The front face is "Welcome back" (login); the back face is "Create your account" (register).
- **Controller wiring** — `AuthenticatedSessionController::create()` now returns `view('auth.combined', ['active' => 'login'])`; `RegisteredUserController::create()` returns `view('auth.combined', ['active' => 'register'])`. The `$active` value controls the initial `is-flipped` class on the card.
- **Flip mechanics** — pure CSS 3D transform (`rotateY(180deg)` on `.flip-card.is-flipped`, `preserve-3d`, `backface-visibility: hidden`). A small vanilla JS IIFE (inlined in the view) toggles the class on `.flip-trigger` clicks and syncs the `.flip-scene` height (login vs register face have different heights) by temporarily un-absolutifying the back face to measure its true height. No page reload on flip.
- **Validation handling** — a failed login/register POST redirects back to `/login` or `/register`, whose `create()` method re-renders `combined.blade.php` with the correct `$active`, so the right face is shown with its red field errors. Field errors are conditionally applied per-face (e.g. `$errors->has('email') && $active === 'login'`).
- **Legacy views retained** — `login.blade.php` and `register.blade.php` are kept as backups but are no longer routed.

### 8.6 Forgot-Password Redesign + Stale Vite Hot-File Cleanup (August 13, 2026)

Two further fixes shipped alongside the flip-card:

1. **`forgot-password.blade.php` redesign** — the default Breeze `<x-guest-layout>` version was replaced with a standalone full-page view matching the combined auth design system (navy gradient, gold dot-grid, EB Garamond/Inter fonts, gold-top-bordered card). It includes a `session('status')` message block, a "Back to sign in" link (`route('login')`), and the bottom trust line — consistent with `combined.blade.php`. The reset link still posts to `route('password.email')`; no controller/route changes were needed.
2. **Stale Vite hot-file cleanup** (`app/Providers/AppServiceProvider.php`) — `npm run dev` (Vite) writes `public/hot` so `@vite` loads assets from the dev server. If the server stops but the file lingers, `@vite` points at a dead port and the app renders a blank screen. The `boot()` method now calls `cleanupStaleViteHotFile()`, which reads `public/hot`, probes the referenced host:port with a fast `fsockopen` (0.3s timeout), and `@unlink`s the file if the server is no longer listening — falling back to compiled `public/build` assets automatically.

### Role-Based Access Control (RBAC)
- **Architecture**: Decoupled from the `users` table. Uses a dedicated `roles` table linked via `user_id` for better scalability.
- **Roles**: `admin` and `user`.
- **User Model Helper**: `isAdmin()` method checks the related `Role` model for the 'admin' name.
- **AdminMiddleware**: Protects all `/admin/*` routes.
- **Role-Aware Root Route**: `/` now checks auth and role — guests → `landing.blade.php`; admins → `redirect(admin.dashboard)`; regular users → `redirect(user.dashboard)`. The SPA itself is reached through the launcher routes `/user/manual` and `/user/auto` (and the `/app/{any?}` catch-all).
- **SPA Protection**: The SPA views (`view('app')`) are served only to authenticated users. After login/register, `bootstrap/app.php`'s `redirectUsersTo()` closure sends admins to `admin.dashboard` and everyone else to `user.dashboard`.
- **Admin `/` Redirect**: A `GET /admin` route was added that redirects to `admin.dashboard` via `route()` helper, since the admin group previously only defined `/admin/dashboard` and bare `/admin` returned 404.
- **Registration**: Public registration (`/register`) defaults to the `user` role and has no role selection field to maintain security.

---

## 9. Mail Configuration (Gmail SMTP)

Password reset emails from Breeze require a working mail driver. Configured with **Gmail SMTP** using an App Password:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=kamran.debug@gmail.com
MAIL_PASSWORD=vwzbztngwpftbjxy
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="kamran.debug@gmail.com"
MAIL_FROM_NAME="ExamCraft Pro"
```

### Notes
- **Gmail App Password** is used (not the account's regular password). App passwords require 2-Step Verification enabled on the Google Account.
- **Password reset emails** confirmed working — reset links arrive in the Gmail inbox and the full reset flow (request → email → link → new password → login) is verified.
- TLS encryption on port 587 is standard for Gmail SMTP submission.

---

## 10. Marketing Landing Page

A standalone Blade template (`landing.blade.php`) with its own CSS (`public/css/landing.css`) serves as the front door for unauthenticated visitors.

### Route Strategy
The root `GET /` route lives **outside** all middleware groups in `routes/web.php`. It checks `auth()->check()`:
- **Guest** → returns `view('landing')` — the marketing page
- **Authenticated** → returns `app(ExamCraftController::class)->index()` — the SPA

After login or registration, Laravel redirects to `/` which automatically serves the SPA.

### Design System — Academic Authority Theme
Built around a Cambridge-inspired palette:
| Variable | Color | Usage |
|----------|-------|-------|
| `--navy` | `#1B2A4A` | Primary bg, navbar, dark sections |
| `--parchment` | `#F7F2E4` | Light section bg |
| `--gold` | `#C9A84C` | Accent, CTA buttons, highlights |
| `--crimson` | `#8B1A1A` | Cambridge red, answer sheet references |
| `--card-dark` | `#243559` | Cards on navy background |

Typography: EB Garamond for headings, Inter for body/UI, IBM Plex Mono for paper codes.

### Page Sections
1. **Sticky Navbar** — transparent→navy on scroll, hamburger on mobile, "Log in" + "Get Started" CTAs
2. **Hero** — 2-column grid with a realistic Cambridge exam paper preview card (PHYSICS 5054/11) + hero text and CTAs
3. **Feature Highlights** — 4 cards (Typography Engine, Block System, Auto-Numbering, PDF Export) with colored icon circles
4. **Block System Showcase** — 3×2 grid of block type cards on navy background with gold top borders
5. **How It Works** — 3-step horizontal flow with numbered circles and dashed connectors
6. **Who It's For** — 3 audience cards (Schools, Private Tutors, University Departments) with gold left accent
7. **CTA Banner** — centered call-to-action on navy: "Ready to set your first paper?"
8. **Footer** — 3-column link grid (Product, Support, Legal) + copyright

### Technical Details
- **Zero dependencies**: No Vite pipeline, no Bootstrap, no Tailwind — pure CSS with Google Fonts CDN
- **Fully responsive**: 1280px desktop · 768px tablet · 375px mobile breakpoints
- **Scroll reveal**: IntersectionObserver adds `.visible` to `.reveal` elements as they enter the viewport
- **Smooth scroll**: Anchor links scroll to sections with `behavior: smooth`
- **Inline SVG only**: No icon libraries or image assets — every icon is an inline SVG

---

## 11. Deployment & Setup Details
1.  **Dependencies**: `composer install` && `npm install`.
2.  **Environment**: Configure `.env` with MySQL credentials and `APP_URL`.
3.  **Database**: `php artisan migrate --seed` (Initializes roles and default admin — user `admin@examcraft.com` with role `admin`). This also runs the `add_grade_to_question_bank_table` migration (auto paper generator), the `create_user_papers_table` migration (My Papers), and the `convert_question_bank_to_json` migration (question bank JSON schema).
4.  **Breeze Install**: Already installed. `composer require laravel/breeze --dev` → `php artisan breeze:install blade` (adds auth controllers, views, routes, and ProfileController). If re-installing, Breeze must NOT be allowed to overwrite `routes/web.php` or `resources/css/app.css` — both have been customized post-install.
5.  **Post-Breeze Checks**: After any Breeze reinstall, verify: (a) `resources/css/app.css` still contains ~1,700 lines of theme CSS (not just 3 Tailwind directives), (b) `AuthenticatedSessionController::store()` redirects via the `isAdmin()` role check (not `route('dashboard')`), (c) `bootstrap/app.php` has `$middleware->redirectUsersTo()` as a role-aware closure AND registers `api:` routing, (d) `resources/views/app.blade.php` uses `->role?->name` nullsafe operator.
6.  **Mail**: Configure `.env` `MAIL_*` settings with Gmail SMTP (App Password required) before testing password reset flows.
7.  **Storage**: `php artisan storage:link` (Critical for image visibility).
8.  **Build**: `npm run dev` (Development with HMR on `http://127.0.0.1:5173`) or `npm run build` (Production Vite bundle). Vite dev server is configured with `host: '127.0.0.1'` and `cors: true` in `vite.config.js`. Stale `public/hot` files are auto-removed on boot by `AppServiceProvider` when the dev server is no longer listening (§8.6), so a dead hot file won't blank the app.
9.  **Auto Paper Generator — Seeding Test Data**: The generator pulls from the `question_bank` table. Seed questions via the Admin Dashboard (`/admin/dashboard` → Question Bank → Add Question) with grade and subject set. The API endpoint (`/api/question-bank/filter?grade=X&subject=Y`) returns questions read from the JSON `data` column.
10.  **User Papers (My Papers)**: Non-admin users manage their saved papers at `/user/dashboard` → "My Papers". The SPA persists papers via `/api/user/papers` (session auth + CSRF from `bootstrap.js`); the Blade dashboard reads/writes them via the `user.papers.*` routes.

---
*Last Updated: August 19, 2026 by ExamCraft AI Assistant*
