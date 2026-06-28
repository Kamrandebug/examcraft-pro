# ExamCraft Pro v36 — Modern Migration Walkthrough

This document provides a comprehensive overview of the **ExamCraft Pro v36** project, which has been successfully migrated from a monolithic HTML file to a modern, modular architecture.

---

## 1. Project Overview
- **What it is**: ExamCraft Pro is a professional exam paper authoring tool. It allows educators to design, format, and export high-quality examination papers with automated numbering, precise typography, and professional layouts.
- **Original State**: A single, 4,000+ line monolithic `v36.html` file containing all CSS, JS, and HTML structure.
- **Final State**: A full-stack Laravel 12 + Vue 3 Single Page Application (SPA).
- **Tech Stack**:
    - **Backend**: Laravel 12 (RESTful JSON API + SPA host).
    - **Frontend**: Vue 3 (Composition API), Vite (Build tool).
    - **State Management**: Pinia (Global reactive stores).
    - **Persistence**: IndexedDB (Local project storage) + MySQL (Server-side persistence via Eloquent).
    - **UI/UX**: Bootstrap 5, Font Awesome 6.5, SortableJS (Drag & Drop), @vueuse/core (Debouncing).
    - **Export**: jsPDF, html2canvas, QRCode.js.

---

## 2. What Was Achieved (Full Feature List)
The migration successfully implemented every core feature of the original version with improved modularity and performance:

- **Block System**:
    - Supported Types: **MCQ**, **Section**, **Text**, **Image**, **Table**, **Divider**.
    - Actions: Add via TopBar, Drag from Left Palette, Reorder (SortableJS), Duplicate, Delete, Move Up/Down.
    - Automation: Auto-renumbering of MCQ questions across multiple pages.
- **Properties Panel (Contextual)**:
    - **MCQ**: Stem, 4 options, Correct answer, Marks, Layout (1/2 col/inline), Image support, Answer boxes toggle.
    - **Section**: Title, Subtitle, Divider toggle, Alignment.
    - **Text**: Rich content, Font size, Alignment, Bold/Italic.
    - **Image**: Base64 upload, Caption, Width, Alignment.
    - **Table**: Rows/Cols, Header management, Variants.
    - **Divider**: Style, Thickness.
- **Paper Metadata**: Full management of Organization, Subject, Duration, Instructions, and Materials.
- **Typography System**: 
    - 6 Cambridge-inspired presets (O Level, A Level, IGCSE, etc.).
    - Centralized `typoStore` and `useTypography` composable injecting 13+ CSS variables (e.g., `--paper-font-family`, `--paper-q-font-size`) into the document root.
    - Pixel-perfect alignment with original `v36.html` CSS class names and verbatim style injection.
- **Theme Engine**: 
    - Day, Afternoon, Night, and Late Night themes.
    - Hour-based auto-detection and persistence.
    - High-stacking context (`z-index: 9999`) for TopBar and dropdowns to ensure visibility over the canvas.
- **Project Management**:
    - Save/Load/Delete from IndexedDB.
    - Export/Import as `.json` files for sharing.
    - **Auto-Save**: Debounced background saving (2s).
- **Export & Print**:
    - Print-ready CSS formatting.
    - Multi-page PDF generation via html2canvas/jsPDF.
    - Answer Key extraction and export.
- **Advanced UI**:
    - **Hardware-Accelerated Zoom**: Uses `scale()` transform and dynamic `marginBottom` calculations to eliminate dead space between A4 pages.
    - Resizable/Collapsible side panels with persistence.
    - Vertical rulers for layout alignment.
    - Keyboard Shortcuts: `Ctrl+S` (Save), `Delete` (Remove block).

---

## 3. Full Project Directory Structure

```text
examcraft-pro/
├── app/
│   ├── Models/                              → 10 Eloquent models (User, ExamPaper, Page, Block, …)
│   │   ├── User.php
│   │   ├── ExamPaper.php
│   │   ├── Page.php
│   │   ├── Block.php
│   │   ├── McqBlock.php
│   │   ├── McqOption.php
│   │   ├── ExamPaperTopic.php
│   │   ├── QuestionBank.php
│   │   ├── QuestionBankOption.php
│   │   └── ProjectSnapshot.php
│   └── Http/
│       └── Controllers/
│           ├── ExamCraftController.php       → Returns app.blade.php view serving the SPA shell
│           ├── UserController.php            → Full CRUD for users
│           ├── ExamPaperController.php       → Full CRUD for exam papers
│           ├── PageController.php            → Nested CRUD (exam-papers.pages)
│           ├── BlockController.php           → Nested CRUD (exam-papers.pages.blocks)
│           ├── McqBlockController.php        → Full CRUD for MCQ-specific block data
│           ├── McqOptionController.php       → Nested CRUD (mcq-blocks.mcq-options)
│           ├── ExamPaperTopicController.php  → Nested CRUD (exam-papers.topics)
│           ├── QuestionBankController.php    → Full CRUD for question bank
│           ├── QuestionBankOptionController.php → Nested CRUD (question-bank.options)
│           └── ProjectSnapshotController.php → Nested CRUD (exam-papers.snapshots)
├── database/
│   └── migrations/
│       ├── 0001_01_01_000001_create_cache_table.php          → Laravel cache table
│       ├── 0001_01_01_000002_create_jobs_table.php           → Laravel queue jobs table
│       ├── 2025_01_01_000001_create_users_table.php          → users, password_reset_tokens, sessions
│       ├── 2025_01_01_000002_create_exam_papers_table.php    → exam_papers (FK → users)
│       ├── 2025_01_01_000003_create_pages_table.php          → pages (FK → exam_papers)
│       ├── 2025_01_01_000004_create_blocks_table.php         → blocks (FK → pages)
│       ├── 2025_01_01_000005_create_mcq_blocks_table.php     → mcq_blocks (FK → blocks)
│       ├── 2025_01_01_000006_create_mcq_options_table.php    → mcq_options (FK → mcq_blocks)
│       ├── 2025_01_01_000007_create_exam_paper_topics_table.php → exam_paper_topics (FK → exam_papers)
│       ├── 2025_01_01_000008_create_question_bank_table.php  → question_bank (FK → users)
│       ├── 2025_01_01_000009_create_question_bank_options_table.php → question_bank_options (FK → question_bank)
│       └── 2025_01_01_000010_create_project_snapshots_table.php → project_snapshots (FK → exam_papers)
├── resources/
│   ├── css/
│   │   └── app.css                       → Full verbatim CSS from v36.html providing the core design
│   ├── js/
│   │   ├── app.js                        → Vue app entry point; initializes Pinia and mounts #app
│   │   ├── App.vue                       → Root component; handles layout, lifecycle, and global events
│   │   ├── stores/
│   │   │   ├── examStore.js              → Manages paper metadata, blocks, pages, and styling states
│   │   │   ├── uiStore.js                → Manages UI state: zoom, panel widths, active tabs, and themes
│   │   │   ├── typoStore.js              → Manages typography settings and Cambridge presets
│   │   │   └── projectStore.js           → Handles IndexedDB operations and project listing
│   │   ├── composables/
│   │   │   ├── useTheme.js               → Logic for theme switching and hour-based auto-detection
│   │   │   ├── useBlockOperations.js     → Core CRUD and reordering logic for exam blocks
│   │   │   ├── useTypography.js          → Injects reactive CSS variables for real-time font updates
│   │   │   ├── useZoom.js                → Logic for canvas scaling and "Fit to Width" calculations
│   │   │   ├── usePanelResize.js         → Handles mouse events for dragging panel resize handles
│   │   │   ├── useRuler.js               → Logic for drawing and syncing vertical rulers on the canvas
│   │   │   ├── useProjectManager.js      → High-level wrapper for saving, loading, and importing projects
│   │   │   ├── usePrint.js               → Orchestrates PDF generation and print window formatting
│   │   │   └── useToast.js               → Simple notification system for user feedback
│   │   └── components/
│   │       ├── TopBar.vue                → Header containing tools, zoom, themes, and project controls
│   │       ├── LeftPanel.vue             → Sidebar for the block palette and question navigation
│   │       ├── CanvasArea.vue            → The main workspace containing the rulers and hardware-accelerated zoom container
│   │       ├── RightPanel.vue            → Tabbed properties editor with 6 icon-based tabs
│   │       ├── canvas/
│   │       │   ├── PageCanvas.vue        → Represents a single A4 page with SortableJS drop zone
│   │       │   ├── BlockRenderer.vue     → Wrapper that renders the correct block type and provides controls
│   │       │   ├── McqBlock.vue          → Component for Multiple Choice Question display
│   │       │   ├── SectionBlock.vue      → Component for Section headers and subtitles
│   │       │   ├── TextBlock.vue         → Component for free-form text or instruction blocks
│   │       │   ├── ImageBlock.vue        → Component for displaying uploaded images with captions
│   │       │   ├── TableBlock.vue        → Component for dynamic data tables and variants
│   │       │   └── DividerBlock.vue      → Component for horizontal page separators
│   │       ├── ui/
│   │       │   ├── VerticalRuler.vue     → SVG-based ruler that updates based on scroll/zoom
│   │       │   ├── ResizeHandle.vue      → Invisible drag area for resizing side panels
│   │       │   ├── MiniRichEditor.vue    → Simple textarea with formatting buttons for metadata
│   │       │   ├── ThemeDropdown.vue     → Theme selection menu with icons and labels
│   │       │   ├── Toast.vue             → Individual notification alert component
│   │       │   ├── ToastContainer.vue    → Fixed position wrapper for stacking notifications
│   │       │   └── ToggleSwitch.vue      → Custom styled checkbox for boolean settings
│   │       └── modals/
│   │           ├── ProjectManagerModal.vue → UI for managing (Save/Load/Import) IndexedDB projects
│   │           ├── PreviewOverlay.vue      → Full-screen print preview and export dashboard
│   │           └── CtVariantModal.vue      → Specialized modal for complex block variant selection
│   └── views/
│       └── app.blade.php                 → Laravel Blade shell with @vite directive and CDN dependencies
├── routes/
│   └── web.php                           → Catch-all SPA route + 10 auth-protected resourceful API routes
├── vite.config.js                        → Vite configuration for Vue 3 and asset bundling
├── package.json                          → Lists dependencies: vue, pinia, sortablejs, @vueuse/core
└── .env                                  → Laravel environment configuration (MySQL connection)
```

---

## 4. Stores — State & Responsibilities

### **examStore.js**
- **State**: `paperMeta` (defaulted to Physics 5054), `pages` (array of pages containing blocks), `styleState` (layout settings), `coverFooter`, `pageFooter`, `selectedBlockId`, `activePageIdx`.
- **Responsibility**: The "Source of Truth" for the exam content.
- **Used by**: `BlockRenderer`, `TopBar`, `RightPanel`, `useBlockOperations`.

### **uiStore.js**
- **State**: `currentZoom` (hardware-accelerated), `leftPanelWidth`, `rightPanelWidth`, `leftCollapsed`, `rightCollapsed`, `activeRTab` (6-tab state), `theme`, `toasts` (array).
- **Responsibility**: Manages the application's interface state, layout, and zoom level.
- **Used by**: `App.vue`, `TopBar`, `RightPanel`, `usePanelResize`.

### **typoStore.js**
- **State**: `typoState` (detailed font/spacing settings), `TYPO_PRESETS` (constant list of Cambridge settings).
- **Responsibility**: Holds the configuration for how the paper looks.
- **Used by**: `RightPanel` (Typography tab), `useTypography`.

### **projectStore.js**
- **State**: `projects` (list from DB), `currentProjectId`.
- **Actions**: `fetchAllProjects`, `saveProject`, `loadProject`, `deleteProject`, `exportJSON`.
- **Responsibility**: Low-level IndexedDB interaction and file I/O.
- **Used by**: `useProjectManager`.

---

## 5. Composables — Logic Breakdown

| Composable | Exports | Touches |
| :--- | :--- | :--- |
| **useBlockOperations** | `addBlock`, `deleteBlock`, `reorder`, `renumber` | `examStore` |
| **useTheme** | `setTheme`, `initTheme` | `uiStore`, `localStorage`, `DOM` |
| **useTypography** | `applyTypoToPaper` | `typoStore`, `CSS Variables` |
| **useZoom** | `zoomIn`, `zoomOut`, `fitToWidth` | `uiStore` |
| **usePanelResize** | `startResize`, `togglePanel` | `uiStore` |
| **useProjectManager** | `saveProject`, `loadProject`, `import/export` | `examStore`, `projectStore`, `typoStore` |
| **usePrint** | `printPaper`, `exportPDF` | `examStore`, `html2canvas`, `jsPDF` |
| **useToast** | `showToast` | `uiStore` |
| **useRuler** | `drawRulers`, `syncScroll` | `DOM`, `uiStore` |

---

## 6. Component Tree

```text
App.vue (useTheme, usePanelResize, useProjectManager)
├── TopBar.vue (useZoom, useBlockOperations, usePrint)
│   ├── ThemeDropdown.vue (useTheme)
│   └── ProjectManagerModal.vue (useProjectManager)
├── LeftPanel.vue (Block Palette)
├── CanvasArea.vue (useRuler)
│   ├── VerticalRuler.vue (Left)
│   ├── PageCanvas.vue (SortableJS)
│   │   └── BlockRenderer.vue (useBlockOperations)
│   │       ├── McqBlock.vue
│   │       ├── SectionBlock.vue
│   │       └── ... (Other Blocks)
│   └── VerticalRuler.vue (Right)
├── RightPanel.vue (useTypography, usePrint)
│   ├── MiniRichEditor.vue
│   └── ToggleSwitch.vue
├── ToastContainer.vue
│   └── Toast.vue
└── PreviewOverlay.vue (usePrint)
```

---

## 7. Data Flow Diagram (User Action → Re-render)

- **Add Block**: 
    User clicks "Add MCQ" → `TopBar.vue` calls `addBlock('mcq')` → `useBlockOperations.js` creates a new object and pushes to `examStore.pages` → Vue's reactivity triggers `PageCanvas.vue` to render a new `BlockRenderer.vue`.
- **Delete Block**: 
    User presses `Delete` → `App.vue` (Global Listener) calls `deleteBlock(selectedId)` → `useBlockOperations.js` removes it and triggers `renumberBlocks()` → UI updates instantly.
- **Typography Change**: 
    User selects "Cambridge A Level" → `RightPanel.vue` updates `typoStore.typoState` → `useTypography.js` watches this and updates `--q-font-size` etc. in the DOM → Canvas styles update immediately.
- **Auto-Save**: 
    User types in a textarea → `examStore` state changes → `App.vue` watch (`useDebounceFn`) triggers after 2s → `useProjectManager.js` saves the entire state to IndexedDB → "Last Saved" badge updates in `TopBar.vue`.

---

## 8. Backend Architecture (New in v36)

### Models Layer
10 Eloquent models under `app/Models/` map the database relationships:

| Model | Table | Key Relationships |
|-------|-------|-------------------|
| User | `users` | Has many ExamPapers, QuestionBank entries |
| ExamPaper | `exam_papers` | Belongs to User; has many Pages, Topics, Snapshots |
| Page | `pages` | Belongs to ExamPaper; has many Blocks |
| Block | `blocks` | Belongs to Page; has one McqBlock (polymorphic via `block_type`) |
| McqBlock | `mcq_blocks` | Belongs to Block (unique); has many McqOptions |
| McqOption | `mcq_options` | Belongs to McqBlock |
| ExamPaperTopic | `exam_paper_topics` | Belongs to ExamPaper |
| QuestionBank | `question_bank` | Belongs to User; has many QuestionBankOptions |
| QuestionBankOption | `question_bank_options` | Belongs to QuestionBank |
| ProjectSnapshot | `project_snapshots` | Belongs to ExamPaper (immutable versioned state) |

### Controllers Layer
10 resource controllers under `app/Http/Controllers/` provide full RESTful CRUD, including nested route model binding for parent-child hierarchies (e.g., `ExamPaper → Page → Block`, `McqBlock → McqOption`).

### Routes
`routes/web.php` defines all resource routes inside an `auth` middleware group:
- **Top-level**: `users`, `exam-papers`, `mcq-blocks`, `question-bank`
- **Nested**: `exam-papers.pages`, `exam-papers.pages.blocks`, `exam-papers.topics`, `exam-papers.snapshots`, `mcq-blocks.mcq-options`, `question-bank.options`

### Database
MySQL database (`examcraft`) with 13 tables — 3 Laravel framework tables (cache, jobs, migrations) + 10 application tables. The migration order follows the FK dependency chain so all foreign keys resolve correctly on `php artisan migrate`.

---

## 9. Build & Run Instructions

### Prerequisites
- **PHP**: 8.2 or higher
- **Composer**: Latest version
- **Node.js**: 18.x or higher
- **NPM**: Latest version
- **MySQL**: 8.0 or higher (XAMPP bundled MySQL is supported)

### Setup Steps
1. **Clone & Install**:
   ```bash
   composer install
   npm install
   ```
2. **Environment**:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   Ensure `.env` has MySQL credentials (see `.env` for `DB_*` keys).
3. **Database**:
   ```bash
   mysql -u root -e "CREATE DATABASE examcraft CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
   php artisan migrate
   ```
4. **Run Development Server**:
   - Terminal 1: `npm run dev` (Vite)
   - Terminal 2: `php artisan serve` (Laravel)
5. **Access**: Open `http://localhost:8000` in your browser.

### Production Build
```bash
npm run build
```

---

## 10. Key Technical Decisions


- **Laravel as Host**: Laravel provides robust routing, authentication, and deployment structure. The API layer (controllers + Eloquent models) now enables server-side persistence alongside the client-side SPA.
- **Eloquent ORM**: 10 models with relationships (belongsTo, hasMany) mirror the exam paper domain. Nested route model binding (`ExamPaper → Page → Block`) keeps controller signatures clean.
- **MySQL over SQLite**: Chosen for production-readiness — concurrent access, user auth, and future cloud sync.
- **Pinia over Vuex**: Pinia's Composition API support makes it more intuitive for modern Vue 3 apps, offering better TypeScript support and less boilerplate.
- **IndexedDB**: Chosen over `localStorage` because exam projects (especially with images) can exceed the 5MB storage limit of `localStorage`.
- **CSS Variables for Typography**: Using centralized CSS variables allowed us to keep the verbatim 1,600+ line CSS file while making it dynamic. Fixed variable naming conflicts to ensure global application across all paper elements.
- **Hardware-Accelerated Zoom**: Transitioned from a simple width-based zoom to a `scale()` based approach. This ensures smooth rendering of complex layouts while using negative margins to manage the resulting whitespace between scaled A4 pages.
- **Z-Index & Layout Fidelity**: Resolved stacking context issues by explicitly setting high z-indexes for the TopBar and RightPanel, ensuring UI controls remain accessible above the transformed canvas layer.
- **CDNs for Heavy Libs**: Font Awesome, Google Fonts, and Bootstrap icons are loaded via CDN to keep the initial bundle size small and leverage browser caching.

---

## 11. Known Limitations & Future Enhancements

- **Undo/Redo**: Currently, the `Ctrl+Z` logic is a placeholder. Implementing a full history stack is the next priority.
- **Question Sidebar**: The LeftPanel question list is currently a static list of blocks; it should be interactive to allow jumping to specific questions.
- **Cloud Sync**: A MySQL backend with a full RESTful API is now in place. The Vue SPA still uses local-first IndexedDB storage; a future step would connect the Pinia stores to the API for remote sync.
- **Collaboration**: Real-time collaborative editing using WebSockets (Laravel Reverb).

---
*Created by ExamCraft AI Assistant — June 2026*
