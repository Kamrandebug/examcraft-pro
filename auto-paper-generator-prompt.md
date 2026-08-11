# ExamCraft Pro v36 — Auto Paper Generator Feature

## MANDATORY FIRST STEP: Read These Files Before Writing Any Code

Read every file in this list completely before touching anything:

1. `resources/js/App.vue`
2. `resources/js/app.js`
3. `resources/js/stores/uiStore.js`
4. `resources/js/stores/examStore.js`
5. `app/Models/QuestionBank.php`
6. `app/Models/QuestionBankOption.php`
7. `app/Http/Controllers/QuestionBankController.php`
8. `routes/api.php`
9. `routes/web.php`
10. `resources/css/app.css` — read only the first 120 lines (CSS variables and theme definitions)
11. `resources/views/app.blade.php`
12. `database/migrations/` — list all files, read the question_bank migration to confirm existing columns

---

## Context

ExamCraft Pro is a Laravel 12 + Vue 3 hybrid app. The SPA shell is served by `app.blade.php`. The SPA uses **no Vue Router** — view switching is done via Pinia store state. App.vue has `overflow: hidden; height: 100vh` on its root `#app` element (do not change this). Axios is pre-configured in `resources/js/bootstrap.js` with CSRF token from the `XSRF-TOKEN` cookie. API routes in `routes/api.php` use the `auth:sanctum` middleware for SPA cookie-based auth.

The `question_bank` table **already has a `subject` column**. Confirm this by reading the migration. Only `grade` needs to be added.

---

## Task Overview

Implement the Auto Paper Generator as a new view inside the existing SPA. When the user logs in, instead of loading the canvas directly, show a Home Screen with two options. Build everything in the existing architecture — no new packages, no Vue Router, no Tailwind.

---

## Step 1 — Database Migration

Run this exact command:

```bash
php artisan make:migration add_grade_to_question_bank_table
```

In the generated migration up() method:

```php
Schema::table('<actual_table_name>', function (Blueprint $table) {
    $table->string('grade')->nullable()->after('subject');
});
```

Replace `<actual_table_name>` with the real table name from the existing question_bank migration.

In down():

```php
Schema::table('<actual_table_name>', function (Blueprint $table) {
    $table->dropColumn('grade');
});
```

Then run:

```bash
php artisan migrate
```

---

## Step 2 — Update QuestionBank Model

File: `app/Models/QuestionBank.php`

Add `'grade'` to the `$fillable` array. Do not change anything else.

---

## Step 3 — API Filter Endpoint

File: `app/Http/Controllers/QuestionBankController.php`

Add this method. Match the exact code style of other methods in this controller:

```php
public function filter(Request $request)
{
    $grade = $request->query('grade');
    $subject = $request->query('subject');

    if (!$grade || !$subject) {
        return response()->json([
            'success' => false,
            'message' => 'Grade and subject are required.',
        ], 422);
    }

    $questions = QuestionBank::with('options')
        ->where('grade', $grade)
        ->where('subject', $subject)
        ->get()
        ->map(function ($q) {
            return [
                'id' => $q->id,
                'question' => $q->question,
                'options' => $q->options->map(function ($opt) {
                    return [
                        'id' => $opt->id,
                        'option_text' => $opt->option_text,
                    ];
                }),
            ];
        });

    return response()->json([
        'success' => true,
        'data' => $questions,
    ]);
}
```

Check the QuestionBankOption model first to confirm the relationship method name is `options` and the column is `option_text`. Adjust if the actual names differ.

---

File: `routes/api.php`

Add the filter route **before** any existing `questionBank` resource route:

```php
Route::get('/question-bank/filter', [QuestionBankController::class, 'filter'])->middleware('auth:sanctum');
```

If `QuestionBankController` is not already imported at the top of `api.php`, add the use statement. Match the import style already present.

---

## Step 4 — Update uiStore.js

File: `resources/js/stores/uiStore.js`

Add `currentView` to the store state. Read the file first to understand the exact store structure (defineStore pattern, state format). Then:

- Add to state: `currentView: 'home'`
- Add action: `setView(view) { this.currentView = view }`

Do not remove or change any existing state, getters, or actions.

---

## Step 5 — Create autoPaperStore.js

File: `resources/js/stores/autoPaperStore.js`

Read an existing store (examStore.js) first to copy the exact defineStore pattern used in this project.

```js
import { defineStore } from 'pinia'
import { computed } from 'vue'

export const useAutoPaperStore = defineStore('autoPaper', {
    state: () => ({
        paperTitle: '',
        schoolName: '',
        paperDate: '',
        grade: '',
        subject: '',
        selectedMcqs: [],
    }),

    getters: {
        totalMarks: (state) => state.selectedMcqs.length,
    },

    actions: {
        setPaperMeta({ title, school, date, grade, subject }) {
            this.paperTitle = title
            this.schoolName = school
            this.paperDate = date
            this.grade = grade
            this.subject = subject
        },

        setSelectedMcqs(mcqs) {
            this.selectedMcqs = mcqs
        },

        reset() {
            this.paperTitle = ''
            this.schoolName = ''
            this.paperDate = ''
            this.grade = ''
            this.subject = ''
            this.selectedMcqs = []
        },
    },
})
```

---

## Step 6 — Register autoPaperStore

File: `resources/js/app.js`

Read this file first. Wherever other stores are imported or initialized (or right after Pinia is set up), add:

```js
import { useAutoPaperStore } from './stores/autoPaperStore'
```

Do not call or use the store here — just import it so Pinia registers it. If the project initializes stores explicitly on app mount, follow that exact pattern. If stores are only imported inside components, skip this step — the component import will register it automatically.

---

## Step 7 — Create HomeScreen.vue

File: `resources/js/views/HomeScreen.vue`

Read `resources/css/app.css` first 120 lines to identify the correct CSS variable names for background color, panel background, text color, border color, and accent/primary color used in this project.

Requirements:
- Composition API `<script setup>`
- Bootstrap 5 grid only — no inline styles except CSS custom property references
- Full height: the root element must fill the parent completely (`height: 100%; display: flex; align-items: center; justify-content: center`)
- Background: use the same CSS variable used for the main canvas or app background
- No emojis in template text

Layout:

```
┌─────────────────────────────────────────┐
│                                         │
│          ExamCraft Pro                  │  ← h2, using --paper-font-family or
│     Professional Exam Authoring         │     equivalent display font var
│                                         │
│   ┌──────────────┐  ┌──────────────┐   │
│   │              │  │              │   │
│   │  [icon]      │  │  [icon]      │   │  ← SVG icons, not Font Awesome
│   │              │  │              │   │     (keep it inline SVG, small)
│   │  Create      │  │  Auto Paper  │   │
│   │  Manual      │  │  Generator   │   │
│   │  Paper       │  │              │   │
│   │              │  │  Select MCQs │   │
│   │  Full design │  │  from bank,  │   │
│   │  control     │  │  auto-build  │   │
│   │              │  │              │   │
│   │ [Start]      │  │ [Generate]   │   │
│   └──────────────┘  └──────────────┘   │
└─────────────────────────────────────────┘
```

Cards use Bootstrap `card` class. Style them with existing CSS variables — panel-bg, border color, text color. The active/hover state should use the primary/accent color variable for border highlight (use `:hover` CSS with a scoped style block).

"Start Designing" button: `uiStore.setView('manual')`
"Generate Paper" button: `uiStore.setView('auto')`

Import and use `useUiStore` from the correct store path (match the import path used in other components).

---

## Step 8 — Create AutoPaperGenerator.vue

File: `resources/js/views/AutoPaperGenerator.vue`

Composition API `<script setup>`. Bootstrap 5. Uses existing CSS variables. No inline styles. No emojis in code. No inline comments.

### Imports needed:
- `useUiStore` from uiStore
- `useAutoPaperStore` from autoPaperStore
- `ref`, `computed`, `watch` from vue
- axios (use `window.axios` — already globally available from bootstrap.js)

### Grade → Subject mapping (hardcoded reactive constant, not in state):

```js
const gradeSubjects = {
  'O Level': ['Physics', 'Chemistry', 'Biology', 'Mathematics', 'Computer Science', 'English Language', 'Urdu', 'Islamiyat', 'Pakistan Studies', 'Economics', 'Commerce', 'Accounting'],
  'A Level': ['Physics', 'Chemistry', 'Biology', 'Mathematics', 'Further Mathematics', 'Computer Science', 'Economics', 'Psychology'],
  '8th Grade': ['General Science', 'Mathematics', 'Urdu', 'English', 'Social Studies', 'Islamiyat', 'Pakistan Studies'],
  '9th Grade': ['Physics', 'Chemistry', 'Biology', 'Mathematics', 'Computer Science', 'Urdu', 'English', 'Islamiyat', 'Pakistan Studies'],
  '10th Grade': ['Physics', 'Chemistry', 'Biology', 'Mathematics', 'Computer Science', 'Urdu', 'English', 'Islamiyat', 'Pakistan Studies'],
}
```

### Reactive state:

```js
const paperTitle = ref('')
const schoolName = ref('')
const paperDate = ref('')
const selectedGrade = ref('')
const selectedSubject = ref('')

const availableSubjects = computed(() => {
  return selectedGrade.value ? gradeSubjects[selectedGrade.value] ?? [] : []
})

const questions = ref([])
const selectedIds = ref(new Set())
const isLoading = ref(false)
const loadError = ref('')
const hasLoaded = ref(false)

const selectedCount = computed(() => selectedIds.value.size)

const canLoad = computed(() =>
  selectedGrade.value !== '' && selectedSubject.value !== ''
)

const canGenerate = computed(() =>
  selectedIds.value.size > 0 &&
  paperTitle.value.trim() !== '' &&
  schoolName.value.trim() !== '' &&
  paperDate.value !== ''
)
```

Watch `selectedGrade`: when it changes, reset `selectedSubject`, `questions`, `selectedIds`, `hasLoaded`, `loadError`.

Watch `selectedSubject`: when it changes, reset `questions`, `selectedIds`, `hasLoaded`, `loadError`.

### loadMcqs function:

```js
async function loadMcqs() {
  isLoading.value = true
  loadError.value = ''
  questions.value = []
  selectedIds.value = new Set()
  hasLoaded.value = false

  try {
    const response = await window.axios.get('/api/question-bank/filter', {
      params: {
        grade: selectedGrade.value,
        subject: selectedSubject.value,
      },
    })

    if (response.data.success) {
      questions.value = response.data.data
      hasLoaded.value = true

      if (questions.value.length === 0) {
        loadError.value = 'No MCQs found for this grade and subject.'
      }
    } else {
      loadError.value = 'Failed to load questions. Please try again.'
    }
  } catch {
    loadError.value = 'Network error. Please check your connection.'
  } finally {
    isLoading.value = false
  }
}
```

### Toggle functions:

```js
function toggleQuestion(id) {
  const newSet = new Set(selectedIds.value)
  if (newSet.has(id)) {
    newSet.delete(id)
  } else {
    newSet.add(id)
  }
  selectedIds.value = newSet
}

function selectAll() {
  selectedIds.value = new Set(questions.value.map((q) => q.id))
}

function deselectAll() {
  selectedIds.value = new Set()
}
```

### generatePaper function:

```js
function generatePaper() {
  const autoPaperStore = useAutoPaperStore()
  autoPaperStore.setPaperMeta({
    title: paperTitle.value.trim(),
    school: schoolName.value.trim(),
    date: paperDate.value,
    grade: selectedGrade.value,
    subject: selectedSubject.value,
  })
  autoPaperStore.setSelectedMcqs(
    questions.value.filter((q) => selectedIds.value.has(q.id))
  )
  uiStore.setView('auto-preview')
}
```

### Template layout:

```
┌─────────────────────────────────────────────┐
│ [← Back to Home]                            │
│                                             │
│ Auto Paper Generator                        │
│                                             │
│ ┌─────────────────────────────────────────┐ │
│ │ PAPER SETTINGS                          │ │
│ │                                         │ │
│ │ Paper Title: [________________]         │ │
│ │ School Name: [________________]         │ │
│ │ Date:        [________________]         │ │
│ │                                         │ │
│ │ Grade:   [dropdown ▼]                   │ │
│ │ Subject: [dropdown ▼] (disabled if no grade) │ │
│ └─────────────────────────────────────────┘ │
│                                             │
│ [Load MCQs]  (disabled if grade/subject empty)│
│                                             │
│ ── [loading spinner] ──                    │
│                                             │
│ ┌─────────────────────────────────────────┐ │
│ │ Found 24 questions                      │ │
│ │ [Select All] [Deselect All]             │ │
│ │ X selected — Total Marks: X             │ │
│ │                                         │ │
│ │ ☐  1. Question text here...             │ │
│ │ ☐  2. Question text here...             │ │
│ │ ☑  3. Question text here...  ← selected│ │
│ └─────────────────────────────────────────┘ │
│                                             │
│ [Generate Paper] (disabled if conditions unmet)│
└─────────────────────────────────────────────┘
```

"← Back to Home" sets `uiStore.setView('home')`.

All form inputs use Bootstrap 5 `form-control` / `form-select` classes. The MCQ list rows use existing panel CSS variables for background and border. Selected rows get a subtle highlight (use CSS class with the accent/primary color variable at low opacity — scoped style).

Do NOT use `<form>` tags — use `<div>` with click handlers.

---

## Step 9 — Create AutoPaperPreview.vue

File: `resources/js/views/AutoPaperPreview.vue`

Composition API `<script setup>`. Read-only. Pulls all data from `useAutoPaperStore()`.

### Template layout:

```
┌────────────────────────────────────────────┐
│ [← Edit Selection]  [Print Paper]  [Home]  │  ← Action bar (hidden on print)
├────────────────────────────────────────────┤
│                                            │
│ ┌──────────────────────────────────────┐  │
│ │         SCHOOL NAME HERE             │  │  ← schoolName (h2, centered)
│ │       Examination Paper              │  │  ← subtitle
│ │                                      │  │
│ │  Grade: O Level  |  Subject: Physics │  │
│ │  Date: 01/08/2025   Total Marks: 20  │  │
│ │ ───────────────────────────────────  │  │
│ │                                      │  │
│ │  Paper Title                         │  │
│ │  ─────────────────────────────────   │  │
│ │                                      │  │
│ │  Section A — Multiple Choice         │  │
│ │  (Each question carries 1 mark)      │  │
│ │                                      │  │
│ │  1.  Question stem text here         │  │
│ │      A.  Option text                 │  │
│ │      B.  Option text                 │  │
│ │      C.  Option text                 │  │
│ │      D.  Option text                 │  │
│ │                                      │  │
│ │  2.  Question stem text here         │  │
│ │      ...                             │  │
│ └──────────────────────────────────────┘  │
└────────────────────────────────────────────┘
```

The paper container should look like a white A4 sheet with a subtle box-shadow, centered on the page background. Use `--paper-font-family` CSS variable for the paper font if it exists (check app.css). Use `--paper-q-font-size` for question text if it exists.

### Print CSS:

Add a `<style>` block (scoped is fine for component styles, but print needs to be unscoped or in a global block):

```css
@media print {
  .action-bar {
    display: none !important;
  }
  body {
    background: white !important;
  }
  .paper-sheet {
    box-shadow: none !important;
    border: none !important;
    margin: 0 !important;
    padding: 0 !important;
  }
}
```

### Actions:

- "Print Paper": `window.print()`
- "← Edit Selection": `uiStore.setView('auto')`
- "Home": `uiStore.setView('home')` then `useAutoPaperStore().reset()`

---

## Step 10 — Update App.vue

File: `resources/js/App.vue`

Read this file completely before editing. Understand its exact structure.

### Imports to add:

```js
import HomeScreen from './views/HomeScreen.vue'
import AutoPaperGenerator from './views/AutoPaperGenerator.vue'
import AutoPaperPreview from './views/AutoPaperPreview.vue'
```

Add these to the component's `components` option (or auto-import via `<script setup>` — match the existing style).

### Template modification:

Identify the root template structure. The **existing canvas/editor layout** (all panels, topbar, canvas, modals) must be wrapped in a single `v-if`:

```html
<template v-if="uiStore.currentView === 'manual'">
  <!-- ALL existing canvas/editor content goes here, unchanged -->
</template>

<HomeScreen v-else-if="uiStore.currentView === 'home'" />

<AutoPaperGenerator v-else-if="uiStore.currentView === 'auto'" />

<AutoPaperPreview v-else-if="uiStore.currentView === 'auto-preview'" />
```

The root element of App.vue (`#app` or equivalent) must remain exactly as-is — do not change its CSS classes, overflow, or height. Only add the conditional rendering inside it.

Import `useUiStore` if it is not already imported in App.vue. Add `const uiStore = useUiStore()` in the setup function or `<script setup>` block.

---

## Step 11 — Final Checks

Run these commands after all file changes:

```bash
php artisan migrate
npm run build
```

If `npm run build` produces errors, run `npm run dev` and check the browser console for Vue errors.

Verify:
- After login, user sees HomeScreen (not the canvas)
- "Start Designing" loads the existing canvas correctly with no visual regressions
- "Generate Paper" loads AutoPaperGenerator
- Load MCQs calls the correct API endpoint
- Selecting MCQs and clicking Generate Paper loads AutoPaperPreview with correct data
- Print button triggers browser print dialog with action bar hidden
- All navigation between views works without page reload
- No existing functionality (canvas, blocks, topbar, auto-save) is broken

---

## Hard Rules

1. Zero inline comments in JS and Vue files
2. No TypeScript — plain JS only
3. No new npm packages
4. No Vue Router
5. No Tailwind classes — Bootstrap 5 only
6. No hardcoded hex colors — use existing CSS variables from app.css
7. Do not modify `resources/css/app.css`
8. Do not modify any Blade auth views (`resources/views/auth/`)
9. Do not modify any existing migration files
10. Do not use `<form>` tags in Vue components — use `<div>` with event handlers
11. `window.axios` is the axios instance — do not import axios separately
12. All API calls must go to `/api/` routes (not `/admin/` routes)
13. If the QuestionBankOption relationship or column name differs from `options` / `option_text`, use the actual names found in the model — do not assume
