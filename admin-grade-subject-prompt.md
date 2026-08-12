# ExamCraft Pro v36 — Admin Question Bank: Grade & Subject Fields

## MANDATORY FIRST STEP: Read These Files Before Writing Any Code

Read every file completely before touching anything:

1. `resources/views/admin/questions/create.blade.php`
2. `resources/views/admin/questions/edit.blade.php`
3. `resources/views/admin/questions/index.blade.php`
4. `resources/views/admin/questions/show.blade.php`
5. `app/Http/Controllers/QuestionBankController.php`
6. `app/Models/QuestionBank.php`
7. `database/migrations/` — list all files, read the question_bank migration AND the add_grade migration to confirm which columns currently exist

---

## Context

The Admin Dashboard uses **AdminLTE v3.2.0 with Bootstrap 4** — not Bootstrap 5. jQuery is globally available. The question bank Create view is a 2-step wizard. Step 1 has Question Text + optional Image. Step 2 has Answer Options A–D (with iCheck radio buttons for "Mark as Correct") plus optional dynamic E–F options.

The `question_bank` table already has a `subject` column. A `grade` column was added in a recent migration. Confirm both exist by reading the migrations before proceeding.

The `subject` column is currently free-text. This is a problem — admin enters "physics" and the SPA filter searches "Physics" and gets no match. This task replaces free-text subject with a strict dropdown on both create and edit, using the same grade→subject mapping that exists in the Vue SPA.

---

## Grade → Subject Mapping

This mapping must be consistent across all blade files. Define it once as a PHP array in the controller, or repeat it in each blade's script block. Use this exact structure:

```php
// PHP version (for controller validation and old() repopulation)
$gradeSubjects = [
    'O Level'   => ['Physics','Chemistry','Biology','Mathematics','Computer Science','English Language','Urdu','Islamiyat','Pakistan Studies','Economics','Commerce','Accounting'],
    'A Level'   => ['Physics','Chemistry','Biology','Mathematics','Further Mathematics','Computer Science','Economics','Psychology'],
    '8th Grade' => ['General Science','Mathematics','Urdu','English','Social Studies','Islamiyat','Pakistan Studies'],
    '9th Grade' => ['Physics','Chemistry','Biology','Mathematics','Computer Science','Urdu','English','Islamiyat','Pakistan Studies'],
    '10th Grade'=> ['Physics','Chemistry','Biology','Mathematics','Computer Science','Urdu','English','Islamiyat','Pakistan Studies'],
];
```

```javascript
// JavaScript version (for jQuery cascade in blade script blocks)
const gradeSubjects = {
    'O Level':   ['Physics','Chemistry','Biology','Mathematics','Computer Science','English Language','Urdu','Islamiyat','Pakistan Studies','Economics','Commerce','Accounting'],
    'A Level':   ['Physics','Chemistry','Biology','Mathematics','Further Mathematics','Computer Science','Economics','Psychology'],
    '8th Grade': ['General Science','Mathematics','Urdu','English','Social Studies','Islamiyat','Pakistan Studies'],
    '9th Grade': ['Physics','Chemistry','Biology','Mathematics','Computer Science','Urdu','English','Islamiyat','Pakistan Studies'],
    '10th Grade':['Physics','Chemistry','Biology','Mathematics','Computer Science','Urdu','English','Islamiyat','Pakistan Studies'],
};
```

---

## Change 1 — `create.blade.php`

### What to add

In **Step 1** (Question Details section), add Grade and Subject dropdowns **before** the Question Text textarea, or **after** the image upload block — wherever they fit cleanly in the existing Bootstrap 4 layout. Do not break the 2-step wizard JS logic.

Add this HTML structure (Bootstrap 4 classes):

```html
<div class="form-group row">
    <label for="grade" class="col-sm-2 col-form-label">Grade <span class="text-danger">*</span></label>
    <div class="col-sm-4">
        <select name="grade" id="grade" class="form-control @error('grade') is-invalid @enderror" required>
            <option value="">— Select Grade —</option>
            <option value="O Level"   {{ old('grade') == 'O Level'    ? 'selected' : '' }}>O Level</option>
            <option value="A Level"   {{ old('grade') == 'A Level'    ? 'selected' : '' }}>A Level</option>
            <option value="8th Grade" {{ old('grade') == '8th Grade'  ? 'selected' : '' }}>8th Grade</option>
            <option value="9th Grade" {{ old('grade') == '9th Grade'  ? 'selected' : '' }}>9th Grade</option>
            <option value="10th Grade"{{ old('grade') == '10th Grade' ? 'selected' : '' }}>10th Grade</option>
        </select>
        @error('grade')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <label for="subject" class="col-sm-2 col-form-label">Subject <span class="text-danger">*</span></label>
    <div class="col-sm-4">
        <select name="subject" id="subject" class="form-control @error('subject') is-invalid @enderror" required disabled>
            <option value="">— Select Grade First —</option>
        </select>
        @error('subject')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
```

### jQuery cascade script

Add this inside the blade file's `@push('scripts')` stack (or `@section('scripts')` — match whichever pattern the file already uses). Place it **after** any existing inline scripts:

```javascript
<script>
$(function () {
    const gradeSubjects = {
        'O Level':   ['Physics','Chemistry','Biology','Mathematics','Computer Science','English Language','Urdu','Islamiyat','Pakistan Studies','Economics','Commerce','Accounting'],
        'A Level':   ['Physics','Chemistry','Biology','Mathematics','Further Mathematics','Computer Science','Economics','Psychology'],
        '8th Grade': ['General Science','Mathematics','Urdu','English','Social Studies','Islamiyat','Pakistan Studies'],
        '9th Grade': ['Physics','Chemistry','Biology','Mathematics','Computer Science','Urdu','English','Islamiyat','Pakistan Studies'],
        '10th Grade':['Physics','Chemistry','Biology','Mathematics','Computer Science','Urdu','English','Islamiyat','Pakistan Studies'],
    };

    const $grade   = $('#grade');
    const $subject = $('#subject');
    const oldGrade   = '{{ old("grade") }}';
    const oldSubject = '{{ old("subject") }}';

    function populateSubjects(grade, preselect) {
        $subject.empty().append('<option value="">— Select Subject —</option>');
        if (!grade || !gradeSubjects[grade]) {
            $subject.prop('disabled', true);
            return;
        }
        gradeSubjects[grade].forEach(function (sub) {
            const selected = (sub === preselect) ? ' selected' : '';
            $subject.append('<option value="' + sub + '"' + selected + '>' + sub + '</option>');
        });
        $subject.prop('disabled', false);
    }

    if (oldGrade) {
        populateSubjects(oldGrade, oldSubject);
    }

    $grade.on('change', function () {
        populateSubjects($(this).val(), '');
    });
});
</script>
```

---

## Change 2 — `edit.blade.php`

### What to add / modify

Read the edit form completely first. It likely has a free-text `<input type="text" name="subject">` field. If it exists, **replace it** with the dropdown. If `grade` does not exist, add it.

Add both dropdowns to the edit form. Pre-populate them from the `$question` Eloquent object. On validation failure, repopulate from `old()`:

```html
<div class="form-group row">
    <label for="grade" class="col-sm-2 col-form-label">Grade <span class="text-danger">*</span></label>
    <div class="col-sm-4">
        <select name="grade" id="grade" class="form-control @error('grade') is-invalid @enderror" required>
            <option value="">— Select Grade —</option>
            @foreach(['O Level','A Level','8th Grade','9th Grade','10th Grade'] as $g)
                <option value="{{ $g }}" {{ old('grade', $question->grade) == $g ? 'selected' : '' }}>{{ $g }}</option>
            @endforeach
        </select>
        @error('grade')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <label for="subject" class="col-sm-2 col-form-label">Subject <span class="text-danger">*</span></label>
    <div class="col-sm-4">
        <select name="subject" id="subject" class="form-control @error('subject') is-invalid @enderror" required>
            <option value="">— Select Subject —</option>
        </select>
        @error('subject')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
```

If the edit form already had a `subject` text input, remove that input entirely — the dropdown above replaces it.

### jQuery cascade script for edit page

```javascript
<script>
$(function () {
    const gradeSubjects = {
        'O Level':   ['Physics','Chemistry','Biology','Mathematics','Computer Science','English Language','Urdu','Islamiyat','Pakistan Studies','Economics','Commerce','Accounting'],
        'A Level':   ['Physics','Chemistry','Biology','Mathematics','Further Mathematics','Computer Science','Economics','Psychology'],
        '8th Grade': ['General Science','Mathematics','Urdu','English','Social Studies','Islamiyat','Pakistan Studies'],
        '9th Grade': ['Physics','Chemistry','Biology','Mathematics','Computer Science','Urdu','English','Islamiyat','Pakistan Studies'],
        '10th Grade':['Physics','Chemistry','Biology','Mathematics','Computer Science','Urdu','English','Islamiyat','Pakistan Studies'],
    };

    const $grade   = $('#grade');
    const $subject = $('#subject');
    const currentGrade   = '{{ old("grade", $question->grade ?? "") }}';
    const currentSubject = '{{ old("subject", $question->subject ?? "") }}';

    function populateSubjects(grade, preselect) {
        $subject.empty().append('<option value="">— Select Subject —</option>');
        if (!grade || !gradeSubjects[grade]) {
            $subject.prop('disabled', true);
            return;
        }
        gradeSubjects[grade].forEach(function (sub) {
            const selected = (sub === preselect) ? ' selected' : '';
            $subject.append('<option value="' + sub + '"' + selected + '>' + sub + '</option>');
        });
        $subject.prop('disabled', false);
    }

    populateSubjects(currentGrade, currentSubject);

    $grade.on('change', function () {
        populateSubjects($(this).val(), '');
    });
});
</script>
```

---

## Change 3 — `index.blade.php`

### Add Grade column to DataTable

Read the file first. Find the `<thead>` row that contains columns like `Question`, `Subject`, `Topic`, `Marks`, `Type`, `Actions`.

Add a `Grade` column header **before** the `Subject` column:

```html
<th>Grade</th>
```

In the `<tbody>` rows (if they are Blade-rendered with `@foreach`), add the matching cell:

```html
<td>{{ $question->grade ?? '—' }}</td>
```

If the DataTable uses **Ajax/server-side rendering** (check for `ajax:` in the DataTables JS init), instead add the column to the DataTables columns config:

```javascript
{ data: 'grade', name: 'grade', defaultContent: '—' },
```

Place it before the `subject` column entry. Also update the server-side response in the controller if it returns JSON for DataTables — add `'grade'` to the returned fields.

---

## Change 4 — `show.blade.php`

Read the file. Find where `subject`, `topic`, `marks` are displayed (likely in a `<dl>`, `<table>`, or definition list). Add Grade display **before** Subject:

```html
<tr>
    <th style="width: 150px;">Grade</th>
    <td>{{ $question->grade ?? '—' }}</td>
</tr>
```

Match the exact HTML tag structure already used for other metadata rows. Do not invent new structure.

---

## Change 5 — `QuestionBankController.php`

### store() method — add grade & subject validation

Read the existing `store()` method. Find the `validate()` or `$request->validate()` call. Add these two rules. Make both required:

```php
'grade'   => 'required|string|in:O Level,A Level,8th Grade,9th Grade,10th Grade',
'subject' => 'required|string|max:100',
```

Then confirm that `grade` and `subject` are saved to the QuestionBank model. Look at how the model is created (`QuestionBank::create(...)` or `$question->save()`). If using `$request->all()` or explicit assignment, add grade explicitly:

```php
$question->grade   = $request->grade;
$question->subject = $request->subject;
```

Or if using mass assignment, ensure `grade` and `subject` are in `$fillable` (already confirmed in the model task above).

### update() method — add grade & subject validation

Find the `update()` validation. Add the same two rules:

```php
'grade'   => 'required|string|in:O Level,A Level,8th Grade,9th Grade,10th Grade',
'subject' => 'required|string|max:100',
```

Ensure both are saved on update. If subject was previously optional or missing from update(), add it now.

### filter() method — already complete

The `filter()` method was added in the previous task. Do not touch it.

---

## Change 6 — `QuestionBank.php` Model

Confirm that `grade` and `subject` are both in the `$fillable` array. If `subject` was already there, only add `grade`. If neither was explicitly in `$fillable` and the model uses `$guarded = []`, no change needed. Read the model first and only edit if necessary.

---

## Final Checks

After all changes, verify:

1. Go to `Admin → Question Bank → Add Question`
   - Grade dropdown shows 5 options
   - Selecting a grade populates Subject dropdown with correct subjects
   - Subject dropdown is disabled until grade is selected
   - Submitting without grade or subject shows validation error
   - Submitting with valid values saves grade and subject to database

2. Go to `Admin → Question Bank → All Questions`
   - Grade column appears in the table next to Subject

3. Go to any question's Show page
   - Grade is displayed in the metadata section

4. Go to any question's Edit page
   - Grade dropdown is pre-selected with the question's existing grade
   - Subject dropdown is pre-populated and pre-selected
   - Changing grade refreshes subject dropdown and clears subject selection
   - Saving updates both grade and subject in database

5. In the Vue SPA Auto Paper Generator:
   - Select O Level → Physics
   - Click Load MCQs
   - Questions added via admin with grade "O Level" and subject "Physics" appear in the list
   - The filter works because the exact string values now match

---

## Hard Rules

1. AdminLTE uses **Bootstrap 4** — use `form-control`, `form-group`, `col-md-*`, not Bootstrap 5 classes
2. jQuery is globally available — `$(function(){})` is the correct document-ready wrapper
3. Do not use `axios` or `fetch` in the admin blade files — this is server-rendered Blade, not a SPA
4. Do not touch any Vue files, Pinia stores, or `resources/js/` in any way
5. Do not touch `resources/css/app.css`
6. Do not touch any auth blade views in `resources/views/auth/`
7. Do not modify the `filter()` API method in the controller — it was already completed
8. Do not create any new migrations — `grade` column already exists from the previous task
9. If the create.blade.php Step 2 wizard has JS that validates or moves between steps, do not break it — read it fully before adding the script block
10. Match the indentation and code style of each file exactly — do not reformat the entire file
11. Subject in the database must store exact strings like "Physics", "O Level" — not lowercase, not abbreviated
