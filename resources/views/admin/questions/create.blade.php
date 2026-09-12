@extends('admin.layouts.master')

@section('title', 'Add Question')
@section('page_title', 'Add Question')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.questions.index') }}">Question Bank</a></li>
    <li class="breadcrumb-item active">Add Question</li>
@endsection

@section('styles')
    <!-- icheck-bootstrap -->
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">
    <style>
        .option-preview-img {
            max-height: 60px;
            margin-top: 5px;
            display: none;
        }
    </style>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8 offset-md-2">
        <form action="{{ route('admin.questions.store') }}" method="POST" enctype="multipart/form-data" id="question-form">
            @csrf

            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">New Question</h3>
                </div>

                <div class="card-body">
                    {{-- Grade + Subject --}}
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
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <label for="subject" class="col-sm-2 col-form-label">Subject <span class="text-danger">*</span></label>
                        <div class="col-sm-4">
                            <select name="subject" id="subject" class="form-control @error('subject') is-invalid @enderror" required disabled>
                                <option value="">— Select Grade First —</option>
                            </select>
                            @error('subject')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Stem text + image --}}
                    <div class="form-group">
                        <label for="stem_text">Question Text <span class="text-danger">*</span></label>
                        <textarea name="stem_text" id="stem_text" class="form-control @error('stem_text') is-invalid @enderror" rows="4" placeholder="Enter question text here...">{{ old('stem_text') }}</textarea>
                        @error('stem_text')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="stem_image">Question Image (optional)</label>
                        <div class="input-group">
                            <div class="custom-file">
                                <input type="file" name="stem_image" class="custom-file-input @error('stem_image') is-invalid @enderror" id="stem_image" onchange="previewImage(this, 'stem-preview-container')">
                                <label class="custom-file-label" for="stem_image">Choose image</label>
                            </div>
                        </div>
                        @error('stem_image')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        <div id="stem-preview-container" class="mt-3" style="display: none;">
                            <img id="stem-image-preview" src="#" alt="Preview" class="img-fluid rounded border" style="max-height: 200px;">
                        </div>
                    </div>

                    <hr>

                    {{-- Dynamic Options (4-10, starting with 4) --}}
                    <h5>Answer Options <span class="text-muted small">(Start with 4, add up to 6 more)</span></h5>

                    <div id="options-container">
                        {{-- Default 4 options (A, B, C, D) - No delete button --}}
                        @foreach(['A', 'B', 'C', 'D'] as $index => $opt)
                            @php $lower = strtolower($opt); @endphp
                            <div class="card card-light border mb-3 option-item" data-option-index="{{ $index }}" data-default="true">
                                <div class="card-body py-2">
                                    <div class="row align-items-end">
                                        <div class="col-sm-2">
                                            <span class="badge badge-primary option-label">Option {{ $opt }}</span>
                                        </div>
                                        <div class="col-sm-5">
                                            <label class="mb-0">Text <span class="text-danger">*</span></label>
                                            <input type="text" name="option_text[]" class="form-control option-text" placeholder="Enter option text" value="{{ old('option_text.'.$index) }}">
                                        </div>
                                        <div class="col-sm-5">
                                            <label class="mb-0">Image (optional)</label>
                                            <div class="custom-file">
                                                <input type="file" name="option_image[]" class="custom-file-input option-image" accept="image/*" onchange="previewOptionImage(this)">
                                                <label class="custom-file-label">Choose image</label>
                                            </div>
                                            <div class="mt-2">
                                                <img src="#" class="option-preview-img img-thumbnail" alt="Preview" style="display:none;">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="form-group">
                        <button type="button" id="add-option-btn" class="btn btn-outline-primary btn-sm" onclick="addOption()">
                            <i class="fa fa-plus"></i> Add Option
                        </button>
                        <small class="text-muted d-block mt-2">Options: <span id="option-count">4</span> / 10</small>
                    </div>

                    <div class="form-group">
                        <label>Correct Answer <span class="text-danger">*</span></label>
                        <div id="correct-answer-container" class="d-flex flex-wrap">
                            @foreach(['A', 'B', 'C', 'D'] as $index => $opt)
                                <div class="icheck-success d-inline mr-4 correct-answer-radio">
                                    <input type="radio" name="correct_answer" value="{{ $index }}" id="correct_{{ $index }}" {{ old('correct_answer') == $index ? 'checked' : '' }}>
                                    <label for="correct_{{ $index }}">{{ $opt }}</label>
                                </div>
                            @endforeach
                        </div>
                        @error('correct_answer')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="card-footer">
                    <a href="{{ route('admin.questions.index') }}" class="btn btn-default">Cancel</a>
                    <button type="submit" class="btn btn-primary float-right">Save Question</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
    <!-- bs-custom-file-input -->
    <script src="{{ asset('adminlte/plugins/bs-custom-file-input/bs-custom-file-input.min.js') }}"></script>

    <script>
        $(function () {
            if (typeof bsCustomFileInput !== 'undefined') {
                bsCustomFileInput.init();
            }
        });

        // ── Grade → Subject cascade ────────────────────────────────────────
        const gradeSubjects = {
            'O Level':   ['Physics','Chemistry','Biology','Mathematics','Computer Science','English Language','Urdu','Islamiyat','Pakistan Studies','Economics','Commerce','Accounting'],
            'A Level':   ['Physics','Chemistry','Biology','Mathematics','Further Mathematics','Computer Science','Economics','Psychology'],
            '8th Grade': ['General Science','Mathematics','Urdu','English','Social Studies','Islamiyat','Pakistan Studies'],
            '9th Grade': ['Physics','Chemistry','Biology','Mathematics','Computer Science','Urdu','English','Islamiyat','Pakistan Studies'],
            '10th Grade':['Physics','Chemistry','Biology','Mathematics','Computer Science','Urdu','English','Islamiyat','Pakistan Studies'],
        };

        const $grade   = $('#grade');
        const $subject = $('#subject');
        const oldGrade   = '{!! addslashes(old('grade', '')) !!}';
        const oldSubject = '{!! addslashes(old('subject', '')) !!}';

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

        // ── Dynamic Options Management ──────────────────────────────────────
        const optionLabels = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J'];
        const MAX_OPTIONS = 10;
        const MIN_OPTIONS = 4;

        function getOptionCount() {
            return $('#options-container .option-item').length;
        }

        function updateOptionLabels() {
            $('#options-container .option-item').each(function (index) {
                $(this).find('.option-label').text('Option ' + optionLabels[index]);
                $(this).data('option-index', index);
            });
        }

        function updateCorrectAnswerRadios() {
            const count = getOptionCount();
            $('#correct-answer-container').empty();

            for (let i = 0; i < count; i++) {
                const label = optionLabels[i];
                const html = `
                    <div class="icheck-success d-inline mr-4 correct-answer-radio">
                        <input type="radio" name="correct_answer" value="${i}" id="correct_${i}">
                        <label for="correct_${i}">${label}</label>
                    </div>
                `;
                $('#correct-answer-container').append(html);
            }
        }

        function updateAddButtonVisibility() {
            const count = getOptionCount();
            if (count >= MAX_OPTIONS) {
                $('#add-option-btn').hide();
            } else {
                $('#add-option-btn').show();
            }
            $('#option-count').text(count);

            // Show delete buttons only for added options (data-option-index >= 4)
            $('#options-container .remove-option').each(function () {
                const $optionItem = $(this).closest('.option-item');
                const optionIndex = parseInt($optionItem.data('option-index'));
                if (optionIndex >= MIN_OPTIONS) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        }

        function addOption() {
            const count = getOptionCount();
            if (count >= MAX_OPTIONS) {
                alert('Maximum 10 options allowed');
                return;
            }

            const newIndex = count;
            const html = `
                <div class="card card-light border mb-3 option-item" data-option-index="${newIndex}" data-default="false">
                    <div class="card-body py-2">
                        <div class="row align-items-end">
                            <div class="col-sm-2">
                                <span class="badge badge-primary option-label">Option ${optionLabels[newIndex]}</span>
                            </div>
                            <div class="col-sm-4">
                                <label class="mb-0">Text <span class="text-danger">*</span></label>
                                <input type="text" name="option_text[]" class="form-control option-text" placeholder="Enter option text">
                            </div>
                            <div class="col-sm-3">
                                <label class="mb-0">Image (optional)</label>
                                <div class="custom-file">
                                    <input type="file" name="option_image[]" class="custom-file-input option-image" accept="image/*" onchange="previewOptionImage(this)">
                                    <label class="custom-file-label">Choose image</label>
                                </div>
                                <div class="mt-2">
                                    <img src="#" class="option-preview-img img-thumbnail" alt="Preview" style="display:none;">
                                </div>
                            </div>
                            <div class="col-sm-3 text-right">
                                <button type="button" class="btn btn-sm btn-danger remove-option" onclick="removeOption(this)">
                                    <i class="fa fa-trash"></i> Delete
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            $('#options-container').append(html);
            if (typeof bsCustomFileInput !== 'undefined') {
                bsCustomFileInput.init();
            }
            updateOptionLabels();
            updateCorrectAnswerRadios();
            updateAddButtonVisibility();
        }

        function removeOption(btn) {
            const $item = $(btn).closest('.option-item');
            const index = $('#options-container .option-item').index($item);

            if (index < MIN_OPTIONS) {
                alert('Cannot delete default options');
                return;
            }

            $item.remove();
            updateOptionLabels();
            updateCorrectAnswerRadios();
            updateAddButtonVisibility();
        }

        // ── Form Validation ─────────────────────────────────────────────────
        $('#question-form').on('submit', function (e) {
            const count = getOptionCount();
            let allFilled = true;

            $('#options-container .option-text').each(function () {
                if ($(this).val().trim() === '') {
                    allFilled = false;
                    $(this).addClass('is-invalid');
                }
            });

            if (!allFilled) {
                e.preventDefault();
                alert('All option texts are required');
                return false;
            }

            if ($('input[name="correct_answer"]:checked').length === 0) {
                e.preventDefault();
                alert('Please select the correct answer');
                return false;
            }
        });

        // ── Image previews ──────────────────────────────────────────────────
        function previewImage(input, containerId) {
            const preview = document.getElementById('stem-image-preview');
            const container = document.getElementById(containerId);

            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    preview.src = e.target.result;
                    container.style.display = 'block';
                };
                reader.readAsDataURL(input.files[0]);
            } else {
                container.style.display = 'none';
            }
        }

        function previewOptionImage(input) {
            const preview = $(input).closest('.custom-file').next('.mt-2').find('img')[0];

            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        // Initialize on page load
        $(document).ready(function () {
            updateAddButtonVisibility();
        });
    </script>
@endsection
