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
│   │   │   ├── AdminController.php            → Admin dashboard with stats aggregation
│   │   │   ├── AuthController.php             → Handles login, register, logout
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
│   └── storage/                              → Symlink to storage/app/public/ (images)
├── resources/
│   ├── css/
│   │   └── app.css                           → Core exam layout CSS (1,600+ lines)
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
│       ├── auth/                             → Blade templates for Login/Register
│       └── app.blade.php                     → SPA Shell (sanitizes window.authUser data)
├── routes/
│   ├── web.php                               → Auth, Admin (prefixed), and SPA routes
│   └── api.php                               → Stateless API routes
├── storage/app/public/
│   ├── questions/                            → Uploaded question images
│   └── options/                              → Uploaded MCQ option images
├── vite.config.js                            → Vite configuration for Vue 3
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
### **Role-Based Access Control (RBAC)**
- **Architecture**: Decoupled from the `users` table. Uses a dedicated `roles` table linked via `user_id` for better scalability.
- **Roles**: `admin` and `user`.
- **User Model Helper**: `isAdmin()` method checks the related `Role` model for the 'admin' name.
- **AdminMiddleware**: Protects all `/admin/*` routes.
- **SPA Protection**: The main ExamCraft SPA (`/`) is protected by the `auth` middleware.
- **Registration**: Public registration (`/register`) defaults to the `user` role and has no role selection field to maintain security.

---

## 9. Deployment & Setup Details
1.  **Dependencies**: `composer install` && `npm install`.
2.  **Environment**: Configure `.env` with MySQL credentials and `APP_URL`.
3.  **Database**: `php artisan migrate --seed` (Initializes roles and default admin).
4.  **Storage**: `php artisan storage:link` (Critical for image visibility).
5.  **Build**: `npm run dev` (Development) or `npm run build` (Production).

---
*Last Updated: August 5, 2026 by ExamCraft AI Assistant*
