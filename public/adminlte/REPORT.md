# AdminLTE v3.2.0 — Complete Project Analysis Report

> **Generated:** 2026-07-29  
> **Project Path:** `K:\adminltev3`  
> **Original Source:** https://adminlte.io (mirrored from `adminlte.io/themes/v3/` via HTTrack)

---

## 1. Executive Summary

This is a **mirrored copy of AdminLTE v3.2.0**, a popular open-source Bootstrap 4 admin dashboard template originally created by **Colorlib**. The project contains **65 HTML pages**, **31 CSS files**, **64 JavaScript files**, and **547 images** (516 of which are flag SVG icons). It serves as a **fully static front-end UI kit** — there is **no server-side code, no build system, no package manager (`package.json`), and no database**. Every page was downloaded from `adminlte.io/themes/v3/` on **March 17, 2026** and carries an HTTrack "mirrored from" comment in its source.

- **Total size:** ~23 MB
- **Total files:** 725
- **Total directories:** 105

---

## 2. Tech Stack

| Layer | Technology | Version |
|-------|-----------|---------|
| **CSS Framework** | Bootstrap | **4.6.1** |
| **JavaScript Library** | jQuery | **3.6.0** |
| **Icon Library** | Font Awesome Free | **5.15.4** |
| **Admin Theme** | AdminLTE | **3.2.0** |
| **UI Widgets** | jQuery UI | **1.13.0** |

### 2.1 Core Files

#### CSS
| File | Role |
|------|------|
| `dist/css/adminlte.min2167.css` | Single compiled & minified theme CSS (contains Bootstrap overrides + AdminLTE components) |

#### JavaScript
| File | Role |
|------|------|
| `dist/js/adminlte2167.js` | Core AdminLTE App JS — **unminified**, ~3300+ lines, defines all jQuery components |
| `dist/js/adminlte.min2167.js` | Minified version of the above |
| `dist/js/demo.js` | **Demo-only** theme customizer (NOT for production) — provides live skin/color switching |
| `dist/js/pages/dashboard.js` | Dashboard scripts (Chart.js charts, jQuery Knob, Sparklines, Sortable) |
| `dist/js/pages/dashboard2.js` | Dashboard v2 scripts |
| `dist/js/pages/dashboard3.js` | Dashboard v3 scripts |

> **Note on `2167` naming:** The numbers in filenames (`adminlte2167.js`, `adminlte.min2167.css`) are non-standard — likely an HTTrack cache-busting artifact. Standard AdminLTE names are `adminlte.js` / `adminlte.min.css`.

---

## 3. Plugin Inventory — 48 Plugin Directories

The `plugins/` directory contains **48 distinct plugin directories** — one of the richest bundled plugin collections in any admin template.

### 3.1 Form & Input Plugins

| Plugin | Version | Key Files | Purpose |
|--------|---------|-----------|---------|
| **bootstrap4-duallistbox** | 4.0.2 | `jquery.bootstrap-duallistbox.min.js`, `.css` | Dual listbox picker |
| **bootstrap-colorpicker** | — | `bootstrap-colorpicker.min.js`, `.css` | Color picker input |
| **bootstrap-slider** | — | `bootstrap-slider.min.js`, `.css` | Slider control |
| **bootstrap-switch** | — | `bootstrap-switch.min.js` | Toggle switch |
| **bs-custom-file-input** | — | `bs-custom-file-input.min.js` | Styled file input |
| **bs-stepper** | 1.7.0 | `bs-stepper.min.js`, `.css` | Wizard / step UI |
| **icheck-bootstrap** | 3.0.1 | `icheck-bootstrap.min.css` | Styled checkboxes/radios |
| **inputmask** | — | `jquery.inputmask.min.js` | Input formatting |
| **ion-rangeslider** | 2.3.1 | `ion.rangeSlider.min.js`, `.css` | Range slider |
| **select2** | — | `select2.full.min.js`, `.css` | Enhanced dropdown |
| **select2-bootstrap4-theme** | — | CSS only | Select2 Bootstrap 4 skin |
| **summernote** | — | `summernote-bs4.min.js`, `.css` | WYSIWYG text editor |

### 3.2 Charting & Data Visualization

| Plugin | Version | Key Files | Purpose |
|--------|---------|-----------|---------|
| **chart.js** | 2.9.4 | `Chart.min.js` | Canvas charts (line, bar, pie, doughnut) |
| **flot** | — | `jquery.flot.js` | jQuery charting library |
| flot/plugins | — | `jquery.flot.pie.js`, `jquery.flot.resize.js` | Flot extensions |
| **sparklines** | — | *(file not found on disk)* | Inline sparkline charts |
| **uplot** | — | `uPlot.iife.min.js`, `uPlot.min.css` | High-perf lightweight charts |
| **jquery-knob** | — | `jquery.knob.min.js` | Dial / gauge widgets |

### 3.3 Data Tables & Grids

| Plugin | Key Files | Purpose |
|--------|-----------|---------|
| **datatables** | `jquery.dataTables.min.js` | Advanced sortable/searchable tables |
| **datatables-bs4** | `dataTables.bootstrap4.min.js`, `.css` | Bootstrap 4 integration |
| **datatables-buttons** | `dataTables.buttons.min.js`, colVis, html5, print | Export (CSV/Excel/PDF/print) |
| **datatables-responsive** | `dataTables.responsive.min.js`, `.css` | Responsive tables |
| **jsgrid** | `jsgrid.min.js`, `.css` | Dynamic data grid with CRUD |

### 3.4 Date/Time & Calendar

| Plugin | Version | Key Files | Purpose |
|--------|---------|-----------|---------|
| **daterangepicker** | 3.1 | `daterangepicker.js`, `.css` | Date range picker |
| **fullcalendar** | — | `main.js`, `main.css` | Full calendar widget |
| **moment** | — | `moment.min.js` | Date/time manipulation |
| **tempusdominus-bootstrap-4** | 5.39.0 | `tempusdominus-bootstrap-4.min.js`, `.css` | DateTime picker |

### 3.5 Maps

| Plugin | Key Files | Purpose |
|--------|-----------|---------|
| **jqvmap** | `jquery.vmap.min.js`, `jquery.vmap.usa.js` | Vector map |
| **jquery-mapael** | `jquery.mapael.min.js` | Advanced vector map rendering |
| **raphael** | `raphael.min.js` | SVG drawing (Mapael dependency) |

### 3.6 Notifications & Overlays

| Plugin | Key Files | Purpose |
|--------|-----------|---------|
| **sweetalert2** | `sweetalert2.min.js` | Modern modal dialogs |
| **sweetalert2-theme-bootstrap-4** | CSS | SweetAlert2 Bootstrap 4 theme |
| **toastr** | `toastr.min.js` | Toast notifications |
| **ekko-lightbox** | `ekko-lightbox.min.js`, `.css` | Image lightbox |

### 3.7 File Upload

| Plugin | Key Files | Purpose |
|--------|-----------|---------|
| **dropzone** | `dropzone.min.js`, `.css` | Drag-and-drop file upload |

### 3.8 Layout & Utilities

| Plugin | Version | Key Files | Purpose |
|--------|---------|-----------|---------|
| **overlayScrollbars** | — | `jquery.overlayScrollbars.min.js`, `.css` | Custom scrollbars |
| **filterizr** | — | `jquery.filterizr.min.js` | Sortable/filterable grid |
| **pace-progress** | 1.0.5 | `pace.min.js` | Page loading progress bar |
| **jquery-mousewheel** | — | `jquery.mousewheel.js` | Mousewheel support |
| **jquery-ui** | 1.13.0 | `jquery-ui.min.js` | Sortable, draggable, dialogs |

### 3.9 Code & Markdown Editors

| Plugin | Key Files | Purpose |
|--------|-----------|---------|
| **codemirror** | `codemirror.js`, `.css`, modes (css, xml, htmlmixed), theme (monokai) | Code editor with syntax highlighting |
| **summernote** | *(see forms)* | WYSIWYG editor |

### 3.10 PDF & Export

| Plugin | Version | Key Files | Purpose |
|--------|---------|-----------|---------|
| **pdfmake** | 0.2.4 | `pdfmake.min.js`, `vfs_fonts.js` | PDF generation (DataTables export) |
| **jszip** | — | `jszip.min.js` | ZIP creation (DataTables export) |

### 3.11 Icons & Flags

| Plugin | Version | Key Files | Purpose |
|--------|---------|-----------|---------|
| **fontawesome-free** | 5.15.4 | `css/all.min.css`, `webfonts/*` | 1500+ icons |
| **flag-icon-css** | — | `css/flag-icon.min.css`, `flags/1x1/*` (258 SVGs), `flags/4x3/*` (258 SVGs) | 258 country flags in 2 aspect ratios |

---

## 4. Project Structure (Full Directory Tree)

```
K:\adminltev3\
│
├── index2.html                              # Main Dashboard (Dashboard v2)
│
├── dist/
│   ├── css/
│   │   └── adminlte.min2167.css             # Single minified theme CSS
│   ├── js/
│   │   ├── adminlte2167.js                  # Core AdminLTE (unminified)
│   │   ├── adminlte.min2167.js              # Core AdminLTE (minified)
│   │   ├── demo.js                          # Theme customizer (DEV ONLY)
│   │   └── pages/
│   │       ├── dashboard.js                 # Dashboard v1 init
│   │       ├── dashboard2.js                # Dashboard v2 init
│   │       └── dashboard3.js                # Dashboard v3 init
│   └── img/
│       ├── AdminLTELogo.png
│       ├── avatar.png / avatar2.png / avatar3.png / avatar4.png / avatar5.png
│       ├── default-150x150.png
│       ├── photo1.png / photo2.png / photo3.jpg / photo4.jpg
│       ├── prod-1.jpg / prod-2.jpg / prod-3.jpg / prod-4.jpg / prod-5.jpg
│       ├── user1-128x128.jpg ... user8-128x128.jpg
│       ├── user2-160x160.jpg
│       └── credit/
│           ├── american-express.png
│           ├── mastercard.png
│           ├── paypal2.png
│           └── visa.png
│
├── pages/
│   ├── calendar.html                        # FullCalendar demo
│   ├── gallery.html                         # Filterizr image gallery
│   ├── kanban.html                          # Kanban board (jQuery UI sortable)
│   ├── widgets.html                         # Widget showcase
│   │
│   ├── UI/
│   │   ├── general.html                     # Colors, badges, pagination, spinners
│   │   ├── buttons.html                     # All button variants
│   │   ├── icons.html                       # Font Awesome 5 icon grid
│   │   ├── modals.html                      # Modals, alerts, toasts
│   │   ├── navbar.html                      # Navbar + tabs + pills
│   │   ├── ribbons.html                     # Card ribbon overlays
│   │   ├── sliders.html                     # Range sliders
│   │   └── timeline.html                    # Vertical timeline
│   │
│   ├── charts/
│   │   ├── chartjs.html                     # Chart.js (line, bar, pie, doughnut)
│   │   ├── flot.html                        # Flot charts
│   │   ├── inline.html                      # Sparkline inline charts
│   │   └── uplot.html                       # uPlot charts
│   │
│   ├── forms/
│   │   ├── general.html                     # Standard form elements
│   │   ├── advanced.html                    # Select2, masks, date range picker, switches
│   │   ├── editors.html                     # Summernote + CodeMirror
│   │   └── validation.html                  # jQuery Validation plugin
│   │
│   ├── tables/
│   │   ├── simple.html                      # Basic Bootstrap tables
│   │   ├── data.html                        # DataTables with export buttons
│   │   └── jsgrid.html                      # jsGrid dynamic grid
│   │
│   ├── layout/
│   │   ├── top-nav.html                     # Top nav only
│   │   ├── top-nav-sidebar.html             # Top nav + sidebar
│   │   ├── boxed.html                       # Boxed layout
│   │   ├── fixed-sidebar.html               # Fixed sidebar
│   │   ├── fixed-sidebar-custom.html        # Fixed sidebar + custom area
│   │   ├── fixed-topnav.html                # Fixed navbar
│   │   ├── fixed-footer.html                # Fixed footer
│   │   ├── collapsed-sidebar.html           # Collapsed sidebar
│   │   └── index3.html                      # Dashboard v3
│   │
│   ├── mailbox/
│   │   ├── mailbox.html                     # Inbox view
│   │   ├── compose.html                     # Compose email
│   │   └── read-mail.html                   # Read email
│   │
│   ├── search/
│   │   ├── simple.html                      # Simple search results
│   │   └── enhanced.html                    # Enhanced search results
│   │
│   └── examples/                            # 26 example pages
│       ├── 404.html                         # Error 404
│       ├── 500.html                         # Error 500
│       ├── blank.html                       # Blank starter
│       ├── contacts.html                    # Contacts list
│       ├── contact-us.html                  # Contact form
│       ├── e-commerce.html                  # E-commerce page
│       ├── faq.html                         # FAQ accordion
│       ├── forgot-password.html             # Forgot password v1
│       ├── forgot-password-v2.html          # Forgot password v2
│       ├── invoice.html                     # Invoice view
│       ├── invoice-print.html               # Print invoice
│       ├── language-menu.html               # Language switcher
│       ├── legacy-user-menu.html            # Legacy user menu
│       ├── lockscreen.html                  # Lock screen
│       ├── login.html                       # Login v1
│       ├── login-v2.html                    # Login v2 (with social buttons)
│       ├── pace.html                        # Pace loading bar
│       ├── profile.html                     # User profile
│       ├── projects.html                    # Projects list
│       ├── project-add.html                 # Add project form
│       ├── project-edit.html                # Edit project form
│       ├── project-detail.html              # Project detail
│       ├── recover-password.html            # Recover password v1
│       ├── recover-password-v2.html         # Recover password v2
│       ├── register.html                    # Register v1
│       └── register-v2.html                 # Register v2
│
└── plugins/                                 # 48 plugin directories
    ├── bootstrap/                           # Bootstrap 4.6.1 (js bundle)
    ├── bootstrap4-duallistbox/              # v4.0.2
    ├── bootstrap-colorpicker/
    ├── bootstrap-slider/
    ├── bootstrap-switch/
    ├── bs-custom-file-input/
    ├── bs-stepper/                          # v1.7.0
    ├── chart.js/                            # v2.9.4
    ├── codemirror/                          # + modes: css, xml, htmlmixed
    ├── datatables/
    ├── datatables-bs4/
    ├── datatables-buttons/
    ├── datatables-responsive/
    ├── daterangepicker/                     # v3.1
    ├── dropzone/
    ├── ekko-lightbox/
    ├── filterizr/
    ├── flag-icon-css/                       # 516 flag SVGs
    ├── flot/
    ├── fontawesome-free/                    # v5.15.4
    ├── fullcalendar/
    ├── icheck-bootstrap/                    # v3.0.1
    ├── inputmask/
    ├── ion-rangeslider/                     # v2.3.1
    ├── jquery/                              # v3.6.0
    ├── jquery-knob/
    ├── jquery-mapael/
    ├── jquery-mousewheel/
    ├── jquery-ui/                           # v1.13.0
    ├── jquery-validation/
    ├── jqvmap/
    ├── jsgrid/
    ├── jszip/
    ├── moment/
    ├── overlayScrollbars/
    ├── pace-progress/                       # v1.0.5
    ├── pdfmake/                             # v0.2.4
    ├── raphael/
    ├── select2/
    ├── select2-bootstrap4-theme/
    ├── simplemde/                           # (referenced but not found on disk)
    ├── sparklines/                          # (referenced but not found on disk)
    ├── summernote/
    ├── sweetalert2/
    ├── sweetalert2-theme-bootstrap-4/
    ├── tempusdominus-bootstrap-4/           # v5.39.0
    ├── toastr/
    └── uplot/
```

---

## 5. Architecture & Design Patterns

### 5.1 How the Project Works

Every file is **100% static**. There is:

| Feature | Status |
|---------|--------|
| Build system (webpack/Vite/Gulp) | ❌ None |
| Package manager (`package.json`) | ❌ None |
| Server-side code (PHP/Node/Python) | ❌ None |
| Database | ❌ None |
| SPA framework (React/Vue/Angular) | ❌ None |
| Runs by opening HTML in browser | ✅ Yes |

### 5.2 Page Layout Template

All 65 HTML pages follow this wrapper hierarchy:

```
body.hold-transition.sidebar-mini.layout-fixed[.dark-mode]
└── div.wrapper
    ├── div.preloader                                    # Animated loading spinner (AdminLTE logo wobble)
    ├── nav.main-header.navbar                           # Top navbar
    │   ├── Left: hamburger menu + breadcrumb links
    │   └── Right: search, messages dropdown, notifications, fullscreen, control sidebar
    ├── aside.main-sidebar.sidebar-dark-primary          # Sidebar
    │   ├── a.brand-link                                 # Logo + brand text
    │   ├── div.sidebar
    │   │   ├── div.user-panel                           # User avatar + name
    │   │   ├── div.form-inline                          # Sidebar search
    │   │   └── nav.mt-2 > ul.nav.nav-sidebar            # Multi-level tree navigation
    ├── div.content-wrapper                              # Main area
    │   ├── div.content-header                           # Page title + breadcrumb
    │   └── section.content                              # Page-specific content (varies)
    ├── aside.control-sidebar.control-sidebar-dark       # Right panel (theme customizer)
    └── footer.main-footer                               # Copyright + version
```

### 5.3 JavaScript Component Architecture

The core JS file (`adminlte2167.js`) defines **15 jQuery components** using a consistent module pattern:

| Component | jQuery Plugin | Description |
|-----------|---------------|-------------|
| `CardRefresh` | `$.fn.CardRefresh` | Load card content via AJAX with overlay |
| `CardWidget` | `$.fn.CardWidget` | Collapse, expand, remove, maximize cards |
| `ControlSidebar` | `$.fn.ControlSidebar` | Right sidebar toggle |
| `DirectChat` | `$.fn.DirectChat` | Chat pane toggle |
| `Dropdown` | `$.fn.Dropdown` | Dropdown enhancements |
| `ExpandableTable` | `$.fn.ExpandableTable` | Expandable table rows |
| `Fullscreen` | `$.fn.Fullscreen` | Fullscreen API toggle |
| `IFrame` | `$.fn.IFrame` | Tabbed iframe panel |
| `Layout` | `$.fn.Layout` | Layout state management |
| `NavbarSearch` | `$.fn.NavbarSearch` | Search bar toggle |
| `PushMenu` | `$.fn.PushMenu` | Sidebar push/collapse |
| `SidebarSearch` | `$.fn.SidebarSearch` | Sidebar filter |
| `TodoList` | `$.fn.TodoList` | Todo check/uncheck |
| `Treeview` | `$.fn.Treeview` | Nested menu navigation |
| `Toasts` | — | Toast notification display |

**Pattern used by every component:**
1. Define `NAME`, `DATA_KEY`, `EVENT_KEY` constants
2. Define `Default` settings object
3. Define class with prototype methods
4. Store instances via `$.data(DATA_KEY, instance)`
5. Expose static `_jQueryInterface` method
6. Attach to `$.fn[NAME]`
7. Auto-init via `$(document).on('click', SELECTOR, handler)` or `$(function() { $(SELECTOR).each(...) })`
8. Provide `noConflict()` restore

### 5.4 Theme Customization System (`demo.js`)

The `demo.js` file injects a control panel into `aside.control-sidebar` that provides real-time theme switching without a page reload:

**Interactive toggles:**
- Dark mode (`body.dark-mode`)
- Fixed header / footer / sidebar
- Sidebar collapse + mini modes (`sidebar-mini`, `sidebar-mini-md`, `sidebar-mini-xs`)
- Nav flat, legacy, compact, child-indent, child-hide styles
- Sidebar no-expand
- Small text on body / navbar / brand / sidebar / footer

**Color variant dropdowns:**
- **Navbar:** 17 dark variants (primary, secondary, info, success, danger, indigo, purple, pink, navy, lightblue, teal, cyan, dark, gray-dark, gray) + 4 light variants (light, warning, white, orange)
- **Sidebar:** 32 total — 16 dark `sidebar-dark-*` + 16 light `sidebar-light-*`
- **Accent color:** 16 accent classes
- **Brand logo:** Same color set as navbar

---

## 6. Page-by-Page Feature Inventory

### 6.1 Dashboard Pages
| Page | Features |
|------|----------|
| `index2.html` | Info boxes (CPU, Likes, Sales, Members), monthly sales line chart (Chart.js), US visitors map (jVectorMap), goal completion progress bars, direct chat, latest members grid, latest orders table with sparklines, browser usage doughnut chart, recently added products list, todo list with jQuery Knob gauges |

### 6.2 UI Elements (8 pages)
| Page | Components |
|------|------------|
| `general.html` | Colors (background/text), progress bars (sizes, colors), notification badges, pagination, borders, dropdowns, navs, loading spinners |
| `buttons.html` | Button styles (primary/secondary/etc.), sizes (xs/sm/lg), outline, block, disabled, button groups, social auth buttons (Google, Facebook, Twitter) |
| `icons.html` | Full Font Awesome 5 grid display |
| `modals.html` | Default modal, modal sizes, modal with form, alerts (success/info/warning/danger), toast notifications |
| `navbar.html` | Navbar layouts, tabs, pills, custom dropdowns |
| `ribbons.html` | Card ribbon overlays (various positions and colors) |
| `sliders.html` | Ion RangeSlider (basic, range, custom), Bootstrap Slider |
| `timeline.html` | Vertical timeline with alternating left/right items |

### 6.3 Form Pages (4)
| Page | Components |
|------|------------|
| `general.html` | Text inputs, selects, checkboxes/radios (iCheck), date picker, color picker, file input |
| `advanced.html` | Select2 (single, multiple, remote data), input masks (phone, date, SSN, IP), date range picker, Bootstrap switch, dual listbox, custom file input, ion range slider |
| `editors.html` | Summernote WYSIWYG (full toolbar), CodeMirror code editor with syntax highlighting |
| `validation.html` | jQuery Validation: required fields, min/max length, email, number range, custom rules, inline error messages |

### 6.4 Chart Pages (4)
| Page | Charts |
|------|--------|
| `chartjs.html` | Area chart, line chart, bar chart, stacked bar chart, donut chart, pie chart (all via Chart.js 2.9.4) |
| `flot.html` | Line chart, bar chart, pie chart (with tooltip plugin) |
| `inline.html` | Inline sparklines for various metrics |
| `uplot.html` | uPlot example charts |

### 6.5 Table Pages (3)
| Page | Features |
|------|----------|
| `simple.html` | Bootstrap 4 striped, bordered, hoverable, contextual tables |
| `data.html` | DataTables with: search, sort, pagination, export buttons (copy, CSV, Excel, PDF, print), column visibility, responsive |
| `jsgrid.html` | jsGrid dynamic grid with insert, update, delete operations |

### 6.6 Mailbox Pages (3)
| Page | Features |
|------|----------|
| `mailbox.html` | Inbox with checkbox select, star, attachment indicators, pagination |
| `compose.html` | Rich text editor (Summernote), To/Cc/Bcc fields, file attachments |
| `read-mail.html` | Email detail with attachment preview/download |

### 6.7 Layout Pages (9)
9 variations demonstrating different layout configurations (see project structure above for full list).

### 6.8 Utility Pages
| Page | Features |
|------|----------|
| `calendar.html` | FullCalendar with month/week/day views, event click/edit |
| `gallery.html` | Filterizr powered image gallery with filter buttons |
| `kanban.html` | Kanban board with draggable cards across columns |
| `widgets.html` | Info boxes, cards, social widgets, chart integration |
| `search/simple.html` | Search results with pagination |
| `search/enhanced.html` | Search results with category tabs |

### 6.9 Example Pages (26)
Covering: **Authentication** — login/register v1 & v2, forgot password, recover password, lock screen; **Error** — 404, 500; **Content** — profile, invoice (view + print), FAQ accordion, contacts, contact form, e-commerce; **Projects** — list, add, edit, detail; **Extras** — blank page, pace loading bar, language menu, legacy user menu

---

## 7. CSS Class Naming Convention

AdminLTE uses a descriptive, readable class system:

| Pattern | Examples | Purpose |
|---------|----------|---------|
| `.main-*` | `.main-header`, `.main-sidebar`, `.main-footer` | Top-level structural containers |
| `.content-*` | `.content-wrapper`, `.content-header` | Content area containers |
| `.brand-*` | `.brand-link`, `.brand-image` | Logo/branding elements |
| `.nav-*` | `.nav-sidebar`, `.nav-treeview`, `.nav-flat` | Navigation classes |
| `.sidebar-*` | `.sidebar-dark-primary`, `.sidebar-mini` | Sidebar theming/layout |
| `.navbar-*` | `.navbar-dark`, `.navbar-light`, `.navbar-primary` | Navbar theming |
| `.accent-*` | `.accent-primary`, `.accent-danger` | Accent color classes |
| `.layout-*` | `.layout-fixed`, `.layout-navbar-fixed` | Layout modifiers |
| `.control-sidebar-*` | `.control-sidebar`, `.control-sidebar-dark` | Control sidebar |
| `.card-*` | `.collapsed-card`, `.maximized-card` | Card state modifiers |
| `.info-box-*` | `.info-box`, `.info-box-icon`, `.info-box-content` | Info metric boxes |
| `.direct-chat-*` | `.direct-chat`, `.direct-chat-msg` | Chat widget |
| `.hold-transition` | `.hold-transition` | Disable transitions during load |

---

## 8. Images & Media Inventory

| Type | Count | Details |
|------|-------|---------|
| Profile/User avatars | 15 | `user*.jpg`, `avatar*.png` |
| Product photos | 5 | `prod-*.jpg` |
| Stock photos | 4 | `photo*.jpg/png` |
| Logo | 1 | `AdminLTELogo.png` |
| Payment card logos | 4 | Visa, Mastercard, Amex, PayPal |
| Country flags (1x1) | 258 | SVG files in `flag-icon-css/flags/1x1/` |
| Country flags (4x3) | 258 | SVG files in `flag-icon-css/flags/4x3/` |
| **Total images** | **547** | |

---

## 9. How HTML Pages Are Structured

Every page uses this consistent bottom-loaded script pattern:

```html
<!-- REQUIRED SCRIPTS -->
<script src="plugins/jquery/jquery.min.js"></script>                          <!-- jQuery 3.6.0 -->
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>         <!-- Bootstrap 4.6.1 -->
<script src="plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
<script src="dist/js/adminlte2167.js?v=3.2.0"></script>                      <!-- AdminLTE core -->

<!-- PAGE-SPECIFIC PLUGINS (varies per page) -->
<script src="plugins/chart.js/Chart.min.js"></script>
<!-- ... other page-specific scripts ... -->

<!-- DEMO ONLY -->
<script src="dist/js/demo.js"></script>
<script src="dist/js/pages/dashboard.js"></script>
```

**Google Font:** Source Sans Pro (300, 400, 400i, 700) — loaded from Google Fonts API.

---

## 10. Important Notes & Observations

### 10.1 About This Copy
- Every HTML file contains: `<!-- Mirrored from adminlte.io/themes/v3/... by HTTrack Website Copier/3.x [XR&CO'2014], Tue, 17 Mar 2026 ... -->`
- The original site is `adminlte.io` — AdminLTE is created by **Colorlib** and is MIT licensed
- Files were captured using **HTTrack** on **March 17, 2026**
- The `2167` suffixes are HTTrack versioning artifacts from the server

### 10.2 Known Missing/Dangling References
| Referenced File | Status |
|-----------------|--------|
| `plugins/sparklines/jquery.sparkline.min.js` | ❌ Not found on disk |
| `plugins/simplemde/simplemde.min.js` | ❌ Not found on disk |
| `iframe.html` (root-level) | ❌ Not found on disk |
| `starter.html` (root-level) | ❌ Not found on disk |
| `index3.html` (root-level) | ❌ Not found on disk (only `pages/layout/index3.html` exists) |

### 10.3 Missing Root-Level Files (referenced in sidebar navigation)
The sidebar menu in `index2.html` links to several root-level pages that don't exist in this mirror:
- `iframe.html` — "Tabbed IFrame Plugin"
- `starter.html` — "Starter Page"
- `index3.html` — referenced as "Home" link

These links will return 404 errors if clicked in the current mirror.

### 10.4 What This Project Is NOT
- ❌ **Not** a Node.js/npm project — no `package.json`, no build scripts
- ❌ **Not** an SPA framework project — no React/Vue/Angular components
- ❌ **Not** production-ready without a backend — all data is static HTML demo content
- ❌ **Not** using modern CSS features like CSS Grid or CSS Custom Properties extensively

### 10.5 What This Project IS Good For
- ✅ **Rapid admin dashboard prototyping** — use pre-built pages and components
- ✅ **Learning reference** — study Bootstrap 4 + jQuery plugin patterns
- ✅ **Plugin showcase** — see 48 plugins working in a unified theme
- ✅ **HTML template base** — integrate with any backend (Rails, Django, Laravel, ASP.NET, etc.)
- ✅ **Design system extraction** — the component library can be extracted and customized

---

## 11. Complete File Count Summary

| File Type | Count |
|-----------|-------|
| `.html` (pages) | 65 |
| `.css` (stylesheet files) | 31 |
| `.js` (JavaScript files) | 64 |
| `.png` / `.jpg` (images) | 31 |
| `.svg` (flag icons) | 516 |
| `.eot` / `.ttf` / `.woff` / `.woff2` (webfonts) | (font-awesome webfonts) |
| **Total files** | **725** |
| **Total directories** | **105** |
| **Total size on disk** | **~23 MB** |

---

## 12. For LLMs: How to Work With This Codebase

1. **This is a static HTML/jQuery/Bootstrap 4 project.** There is no build step — any edits to `.html`, `.css`, or `.js` files take effect immediately on browser refresh.
2. **Main entry point:** `index2.html` — open this in a browser to see the dashboard.
3. **Theme customization:** modify `dist/css/adminlte.min2167.css` for visual changes, or toggle classes on `<body>` (`dark-mode`, `sidebar-collapse`, `layout-fixed`, etc.).
4. **Adding new pages:** copy any existing page template, change the `<title>`, breadcrumb, and content inside `<section class="content">`.
5. **Plugin integration:** all plugins are in `/plugins/` — link them via `<script>` and `<link>` tags in the page header/footer following existing patterns.
6. **AJAX data loading:** the `CardRefresh` component can load remote content into cards. For real data, replace static HTML with AJAX calls to your backend API.
7. **Responsive behavior:** uses Bootstrap 4 grid breakpoints + AdminLTE sidebar classes (`sidebar-mini`, `sidebar-mini-md`, `sidebar-mini-xs`).
8. **Color system:** uses Bootstrap utility classes (`bg-primary`, `text-danger`), AdminLTE theme classes (`sidebar-dark-primary`, `accent-warning`), and the `demo.js` customizer interactively swaps these classes.

---

## 13. Appendix: How the Project Was Obtained

```
Source URL:        https://adminlte.io/themes/v3/
Access tool:       HTTrack Website Copier 3.x
Mirror date:       Tue, 17 Mar 2026 ~12:47 GMT
License:           MIT (original AdminLTE is MIT licensed by Colorlib)
```
