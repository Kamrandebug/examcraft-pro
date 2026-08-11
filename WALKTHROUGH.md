# ExamCraft Pro v36 — Modern Migration Walkthrough

This document provides a comprehensive technical overview of the **ExamCraft Pro v36** project. It serves as a source of truth for understanding the architecture, feature set, and implementation details of the migration from a monolithic HTML template to a professional full-stack application.

---

## 1. Project Mission
ExamCraft Pro is a professional exam paper authoring tool designed for educators. It enables the creation of high-quality examination papers with automated numbering, precise Cambridge-style typography, and professional layouts, while providing both a reactive local-first SPA experience and a robust server-side administration dashboard.

---

## 2. Architecture & Tech Stack
The project is built as a **Hybrid Full-Stack Application**:

### **Backend (Laravel 12)**
- **Role**: Serves as the API layer, authentication provider, and host for the administration dashboard.
- **Persistence**: MySQL (Server-side storage) + Eloquent ORM.
- **Routing**: RESTful resource controllers with nested route model binding.
- **Templating**: Laravel Blade (used for Auth pages and the Admin Dashboard).

### **Frontend (Vue 3 SPA)**
- **Role**: The core "Exam Designer" interface.
- **Architecture**: Composition API with modular components and composables.
- **State Management**: Pinia (Local reactive stores).
- **Persistence**: IndexedDB (via `projectStore`) for local-first, offline-capable project management.
- **Build Tool**: Vite (handles HMR, asset bundling, and CSS processing).

### **Key Dependencies**
- **UI**: Bootstrap 5 (SPA), Bootstrap 4 (AdminLTE Dashboard), Font Awesome 6.5.
- **Interactions**: SortableJS (Drag & Drop), @vueuse/core (Debounced saving).
- **Export**: jsPDF, html2canvas (PDF generation), QRCode.js.

---

## 3. Full Project Directory Structure

```text
examcraft-pro/
├── app/
│   ├── Models/                              → 11 Eloquent models (User, Role, ExamPaper, Page, …)
│   │   ├── User.php                         → HasOne(Role) relationship
│   │   ├── Role.php                         → Dedicated roles table (admin | user)
│   │   ├── ExamPaper.php
│   │   ├── Page.php
│   │   ├── Block.php
│   │   ├── McqBlock.php
│   │   ├── McqOption.php
│   │   ├── ExamPaperTopic.php
│   │   ├── QuestionBank.php
│   │   ├── QuestionBankOption.php
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
│   │   │   ├── QuestionBankController.php      → Full CRUD for question bank (admin & API routes)
│   │   │   ├── QuestionBankOptionController.php → Nested CRUD (question-bank.options)
│   │   │   └── Controller.php                  → Base controller
│   │   └── Middleware/
│   │       └── AdminMiddleware.php           → RBAC check for admin access (via $user->isAdmin())
│   └── Providers/
├── bootstrap/
│   └── app.php                               → Middleware & routing registration
├── config/                                   → Laravel system configuration
├── database/
│   └── migrations/                           → 15 migrations defining the examcraft schema
├── public/
│   ├── adminlte/                             → Centralized AdminLTE v3.2.0 assets (dist, plugins)
│   ├── css/
│   │   └── landing.css                       → Standalone marketing landing page styles (960+ lines)
│   └── storage/                              → Symlink to storage/app/public/ (images)
├── resources/
│   ├── css/
│   │   └── app.css                           → Theme system (1,700+ lines) + Tailwind directives. 4 themes (Day/Afternoon/Night/Late Night) with CSS custom properties, paper typography variables, and all component styles (topbar, panels, canvas, blocks, rulers, modals, toasts, etc.)
│   ├── js/
│   │   ├── app.js                            → Vue app entry point; initializes Pinia
│   │   ├── App.vue                           → Root component; handles layout & auto-save
│   │   ├── stores/                           → Pinia state (exam, ui, typo, project)
│   │   ├── composables/                      → Reusable logic (useZoom, useTypography, etc.)
│   │   └── components/                       → UI components (Canvas, Panels, Blocks)
│   └── views/
│       ├── admin/                            → Blade templates for Admin Dashboard
│       │   ├── users/                        → User management views (index, create, edit, show)
│       │   ├── questions/                    → Question bank views (index, create, show, edit)
│       │   ├── papers/                      → Exam papers views (index, create, show, edit)
│       │   └── dashboard.blade.php           → Dashboard landing page
│       ├── auth/                             → Blade templates for Login/Register (Breeze v2.4.2)
│       │   ├── login.blade.php                → Login form
│       │   ├── register.blade.php             → Registration form
│       │   ├── forgot-password.blade.php       → Password reset request
│       │   ├── reset-password.blade.php        → New password form
│       │   ├── verify-email.blade.php          → Email verification notice
│       │   └── confirm-password.blade.php      → Password confirmation gate
│       ├── dashboard.blade.php                → Breeze authenticated dashboard
│       ├── app.blade.php                     → SPA Shell (sanitizes window.authUser data)
│       └── landing.blade.php                 → Marketing landing page (guests only)
├── routes/
│   ├── web.php                               → Guest routes (login, register, landing), root GET / (guest→landing, auth→SPA), Admin (prefixed + bare / redirect), and SPA routes
│   ├── auth.php                              → Breeze v2.4.2 authentication routes (login, register, password reset, email verification, confirm-password, logout)
│   └── api.php                               → Stateless API routes
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
    *   **Index (DataTables List)**: Searchable, paginated table showing all questions with columns for question text, subject, topic, marks, and type. Export buttons (Copy, CSV, Excel, PDF, Print) with SweetAlert2 delete confirmations (`resources/views/admin/questions/index.blade.php`).
    *   **Create (2-step form)**: Step 1 captures question text (with live character counter) and optional image upload with preview. Step 2 captures answer options A–D (mandatory, text and/or image each, with iCheck "Mark as Correct" radio), plus dynamic E–F (added/removed via JavaScript). A step indicator pill shows progress. All data posts to `admin.questions.store` in one request — the controller validates and saves the question with its options together (`resources/views/admin/questions/create.blade.php`).
    *   **Show (detail view)**: Displays question text, image, all options in a table with correct-answer highlighting, and metadata (subject, topic, marks) (`resources/views/admin/questions/show.blade.php`).
    *   **Edit**: Form to update question text, metadata fields (subject, topic, marks, difficulty, type, correct answer), and upload a replacement image with live preview. Shows the current image if one exists (`resources/views/admin/questions/edit.blade.php`).
    *   The legacy standalone **Manage Options** page was removed when options were merged into the create flow: its sidebar nav item, the `admin/options` GET/POST routes, and `resources/views/admin/questions/options.blade.php` are gone. `QuestionBankOptionController` and the nested `question-bank.options` resource routes are retained.
4.  **Exam Papers**:
    *   **Index (DataTables List)**: Searchable, paginated table of all exam papers with title, subject, exam code, year, and status columns. Export support and SweetAlert2 delete confirmations (`resources/views/admin/papers/index.blade.php`).
    *   **Create**: Form capturing all exam paper metadata — title, subject, exam code, organization, session, year, duration, typography preset, instructions, and materials (`resources/views/admin/papers/create.blade.php`).
    *   **Show (detail view)**: Displays all paper metadata including status badge, pages overview with block counts per page, and related topics/snapshots (`resources/views/admin/papers/show.blade.php`).
    *   **Edit**: Form to update all paper metadata including the status toggle (Draft / Published) (`resources/views/admin/papers/edit.blade.php`).
5.  **DataTables**: Integrated into `index.blade.php` for questions, papers, and dashboard tables, providing client-side search, sort, and export (CSV/Excel/PDF/Print) via the DataTables Buttons plugin. Powered by AdminLTE's bundled plugins (JSZip, pdfmake).

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

#### 8.2 Login Redirect — `/dashboard` vs `/`
Breeze defaults to redirecting authenticated users to `route('dashboard')` (`/dashboard`). Changed in two places:

1. **`AuthenticatedSessionController.php`** — `redirect()->intended()` target changed from `route('dashboard', absolute: false)` to `'/'`
2. **`bootstrap/app.php`** — Added `$middleware->redirectUsersTo('/')` so Breeze's auth middleware redirects authenticated guests to the SPA
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
| `login.blade.php` | Login form |
| `register.blade.php` | Registration form |
| `forgot-password.blade.php` | Password reset request |
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

### Role-Based Access Control (RBAC)
- **Architecture**: Decoupled from the `users` table. Uses a dedicated `roles` table linked via `user_id` for better scalability.
- **Roles**: `admin` and `user`.
- **User Model Helper**: `isAdmin()` method checks the related `Role` model for the 'admin' name.
- **AdminMiddleware**: Protects all `/admin/*` routes.
- **SPA Protection**: The main ExamCraft SPA (`/`) is served only to authenticated users via `view('app')` in a route closure. Guests visiting `/` are shown the marketing landing page (`landing.blade.php`) instead. After login/register, users are redirected to `/` which now loads the SPA automatically.
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
3.  **Database**: `php artisan migrate --seed` (Initializes roles and default admin — user `admin@examcraft.com` with role `admin`).
4.  **Breeze Install**: Already installed. `composer require laravel/breeze --dev` → `php artisan breeze:install blade` (adds auth controllers, views, routes, and ProfileController). If re-installing, Breeze must NOT be allowed to overwrite `routes/web.php` or `resources/css/app.css` — both have been customized post-install.
5.  **Post-Breeze Checks**: After any Breeze reinstall, verify: (a) `resources/css/app.css` still contains ~1,700 lines of theme CSS (not just 3 Tailwind directives), (b) `AuthenticatedSessionController::store()` redirects to `'/'` not `route('dashboard')`, (c) `bootstrap/app.php` has `$middleware->redirectUsersTo('/')`, (d) `resources/views/app.blade.php` uses `->role?->name` nullsafe operator.
6.  **Mail**: Configure `.env` `MAIL_*` settings with Gmail SMTP (App Password required) before testing password reset flows.
7.  **Storage**: `php artisan storage:link` (Critical for image visibility).
8.  **Build**: `npm run dev` (Development with HMR on `http://127.0.0.1:5173`) or `npm run build` (Production Vite bundle). Vite dev server is configured with `host: '127.0.0.1'` and `cors: true` in `vite.config.js`.

---
*Last Updated: August 11, 2026 by ExamCraft AI Assistant*
