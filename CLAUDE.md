# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

ExamCraft Pro v36 — a professional exam paper authoring tool. A Laravel 12 + Vue 3 SPA that was migrated from a monolithic `v36.html` (284KB). The Laravel backend is a thin host serving the SPA; all application logic is client-side.

## Commands

```bash
# Full dev stack (server + queue + logs + Vite concurrently)
composer run dev

# Run PHP tests (PHPUnit with SQLite in-memory)
composer run test

# Run a single PHP test
php artisan test --filter=TestName

# Frontend build
npm run build

# Frontend dev (Vite only)
npm run dev

# Install everything from scratch
composer run setup

# Laravel dev server only
php artisan serve

# PHP CS fixer (Laravel Pint)
./vendor/bin/pint
```

## Architecture

### Backend (Laravel 12)

Very thin — routes/web.php has a single catch-all route that serves `resources/views/app.blade.php`. The blade file loads:
- CDN resources (Bootstrap 5, Font Awesome 6.5, SortableJS, QRCode.js, html2canvas, jsPDF)
- Vite-bundled `resources/js/app.js` and `resources/css/app.css`

Controllers: `ExamCraftController` (1 method returning the view). No database migrations or models used by the app logic.

### Frontend (Vue 3 SPA)

**Entry**: `resources/js/app.js` → creates Vue app, installs Pinia, mounts `#app`.

**Root**: `App.vue` — grid layout with TopBar, LeftPanel/RightPanel (collapsible), CanvasArea, and overlays (ProjectManagerModal, PreviewOverlay, ToastContainer).

### Pinia Stores

| Store | File | Purpose |
|-------|------|---------|
| examStore | `stores/examStore.js` | Paper metadata, blocks, pages, styling, selected block |
| uiStore | `stores/uiStore.js` | Zoom, panel widths, active tab, theme, toasts |
| typoStore | `stores/typoStore.js` | Typography settings + 6 Cambridge presets |
| projectStore | `stores/projectStore.js` | IndexedDB CRUD, import/export |

### Composables

All in `resources/js/composables/`:

| Composable | Purpose |
|------------|---------|
| useBlockOperations | Add/delete/reorder/renumber blocks |
| useTheme | Theme switching with hour-based auto-detection |
| useTypography | Applies typo CSS variables to document root |
| useZoom | Canvas scale() zoom + fit-to-width |
| usePanelResize | Draggable panel resize handles |
| useRuler | SVG-based vertical rulers synced to scroll/zoom |
| useProjectManager | High-level save/load/import/export wrapper |
| usePrint | PDF generation (html2canvas + jsPDF) |
| useToast | Toast notification system |

### Block System

6 block types, each with its own component in `resources/js/components/canvas/`:
- **MCQ** — stem + 4 options + correct answer + marks + layout variants
- **Section** — title/subtitle with divider toggle
- **Text** — rich content with formatting
- **Image** — base64 upload with caption
- **Table** — dynamic rows/cols with header management
- **Divider** — horizontal rule with style/thickness

`BlockRenderer.vue` dispatches to the correct block component. `PageCanvas.vue` wraps each A4 page with SortableJS drag-and-drop.

### Component Tree

```
App.vue
├── TopBar.vue (useZoom, useBlockOperations, usePrint)
│   ├── ThemeDropdown.vue
│   └── ProjectManagerModal.vue
├── LeftPanel.vue (Palette)
├── CanvasArea.vue
│   ├── VerticalRuler.vue (x2)
│   ├── PageCanvas.vue (SortableJS)
│   │   └── BlockRenderer.vue → {Mcq,Section,Text,Image,Table,Divider}Block.vue
│   └── VerticalRuler.vue
├── RightPanel.vue (6-tab properties editor)
├── ToastContainer → Toast.vue
└── PreviewOverlay.vue
```

### Key Technical Decisions

- **IndexedDB** over localStorage (5MB limit) for project persistence with images
- **Scale-based zoom** using CSS `transform: scale()` with negative margins for whitespace management
- **CSS variables** for dynamic typography (injected by `useTypography` composable)
- **Auto-save** debounced at 2s via `@vueuse/core` useDebounceFn
- **No Laravel backend database** — fully client-side; the Laravel app is just a host
- **CDNs** for heavy libs (Bootstrap, Font Awesome, Google Fonts) to keep Vite bundle small

### Keyboard Shortcuts

- `Ctrl+S` — Save project
- `Delete` — Remove selected block (not in input fields)
