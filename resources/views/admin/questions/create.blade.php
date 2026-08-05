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
        .step-pill {
            display: inline-flex;
            align-items: center;
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

                {{-- Step Indicator --}}
                <div class="card-body border-bottom pb-2">
                    <div class="d-flex">
                        <div class="step-pill mr-2">
                            <span class="badge badge-primary" id="step-1-badge">1</span>
                            <span class="ml-1" id="step-1-label">Question Details</span>
                        </div>
                        <div class="step-pill">
                            <span class="badge badge-light border" id="step-2-badge">2</span>
                            <span class="ml-1" id="step-2-label">Answer Options</span>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    {{-- ============================================================
                         STEP 1 — Question Details
                    ============================================================ --}}
                    <div id="step-1">
                        <h5 class="mb-3">Step 1 of 2 — Question Details</h5>

                        <div class="form-group">
                            <label for="question_text">Question Text</label>
                            <textarea name="question_text" id="question_text" class="form-control @error('question_text') is-invalid @enderror" rows="5" placeholder="Enter question text here...">{{ old('question_text') }}</textarea>
                            <small id="char-count" class="form-text text-muted">0 characters</small>
                            @error('question_text')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div id="step1-error" class="text-danger small" style="display: none;">Please enter question text or select an image.</div>
                        </div>

                        <div class="form-group">
                            <label for="question_image">Question Image (Optional)</label>
                            <div class="input-group">
                                <div class="custom-file">
                                    <input type="file" name="question_image" class="custom-file-input @error('question_image') is-invalid @enderror" id="question_image" onchange="previewImage(this, 'preview-container')">
                                    <label class="custom-file-label" for="question_image">Choose image</label>
                                </div>
                            </div>
                            <div id="preview-container" class="mt-3" style="display: none;">
                                <img id="image-preview" src="#" alt="Preview" class="img-fluid rounded border" style="max-height: 250px;">
                            </div>
                        </div>
                    </div>

                    {{-- ============================================================
                         STEP 2 — Answer Options
                    ============================================================ --}}
                    <div id="step-2" style="display: none;">
                        <h5 class="mb-3">Step 2 of 2 — Answer Options</h5>

                        @php
                            $optionLabels = ['A', 'B', 'C', 'D', 'E', 'F'];
                        @endphp

                        @foreach($optionLabels as $opt)
                            <div class="option-row card card-light border mb-3 {{ $opt <= 'D' ? '' : 'd-none' }}" data-label="{{ $opt }}" id="option-row-{{ $opt }}">
                                <div class="card-body">
                                    @if($opt > 'D')
                                        <span class="text-danger float-right remove-option-btn" style="cursor: pointer;" onclick="removeOption('{{ $opt }}')">
                                            <i class="fas fa-times-circle"></i> Remove
                                        </span>
                                    @endif

                                    <div class="d-flex align-items-center mb-2">
                                        <span class="badge badge-primary mr-2">Option {{ $opt }}</span>
                                        <div class="icheck-success d-inline">
                                            <input type="radio" name="correct_option" value="{{ $opt }}" id="correct_{{ $opt }}">
                                            <label for="correct_{{ $opt }}">Mark as Correct</label>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label>Option Text</label>
                                        <input type="text" name="options[{{ $opt }}][text]" class="form-control" placeholder="Enter option text">
                                    </div>

                                    <div class="form-group mb-0">
                                        <label>Option Image (Optional)</label>
                                        <div class="input-group">
                                            <div class="custom-file">
                                                <input type="file" name="options[{{ $opt }}][image]" class="custom-file-input" id="option_image_{{ $opt }}" onchange="previewOptionImage(this, 'option-preview-{{ $opt }}')">
                                                <label class="custom-file-label" for="option_image_{{ $opt }}">Choose image</label>
                                            </div>
                                        </div>
                                        <div id="option-preview-{{ $opt }}" class="mt-2">
                                            <img src="#" class="option-preview-img img-thumbnail" alt="Option {{ $opt }} Preview">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        <div id="add-option-btn-wrap" class="mb-3">
                            <button type="button" class="btn btn-outline-success btn-sm" id="add-option-btn" onclick="addOption()">
                                <i class="fas fa-plus"></i> <span id="add-option-text">Add Option E</span>
                            </button>
                        </div>

                        <div id="step2-error" class="text-danger small mb-2" style="display: none;">Please mark one option as the correct answer.</div>
                    </div>
                </div>

                <div class="card-footer">
                    <a href="{{ route('admin.questions.index') }}" class="btn btn-default" id="cancel-btn">Cancel</a>
                    <button type="button" id="nextToStep2" class="btn btn-primary float-right">Next →</button>
                    <button type="button" id="backToStep1" class="btn btn-default" style="display: none;">← Back</button>
                    <button type="submit" id="save-question-btn" class="btn btn-success float-right" style="display: none;">Save Question</button>
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
        const optionLabels = ['A', 'B', 'C', 'D', 'E', 'F'];
        const maxOptions = 6;
        let visibleOptionCount = 4;

        $(function () {
            // Initialize custom file input for all file inputs
            if (typeof bsCustomFileInput !== 'undefined') {
                bsCustomFileInput.init();
            }

            // Live character counter for question_text
            $('#question_text').on('input', function () {
                $('#char-count').text($(this).val().length + ' characters');
            });

            // ── Step navigation ────────────────────────────────────────────
            $('#nextToStep2').on('click', function () {
                if (!validateStep1()) return;
                showStep(2);
            });

            $('#backToStep1').on('click', function () {
                showStep(1);
            });

            // ── Correct-answer validation on submit ─────────────────────────
            $('#question-form').on('submit', function (e) {
                if (!$('input[name="correct_option"]:checked').length) {
                    e.preventDefault();
                    showStep(2);
                    $('#step2-error').show();
                    return false;
                }
                $('#step2-error').hide();
            });
        });

        // ── Step 1 validation ───────────────────────────────────────────────
        function validateStep1() {
            const hasText = $('#question_text').val().trim().length > 0;
            const hasImage = $('#question_image').get(0).files.length > 0;
            const hasError = !hasText && !hasImage;

            $('#step1-error').toggle(hasError);
            $('#question_text').toggleClass('is-invalid', hasError);
            $('#question_image').toggleClass('is-invalid', hasError);

            return !hasError;
        }

        // ── Step switching ──────────────────────────────────────────────────
        function showStep(step) {
            const onStep2 = step === 2;
            $('#step-1').toggle(!onStep2);
            $('#step-2').toggle(onStep2);

            $('#nextToStep2').toggle(!onStep2);
            $('#cancel-btn').toggle(!onStep2);
            $('#backToStep1').toggle(onStep2);
            $('#save-question-btn').toggle(onStep2);

            // Update step indicator pills
            $('#step-1-badge')
                .removeClass('badge-primary badge-success badge-light border')
                .addClass(onStep2 ? 'badge-success' : 'badge-primary');
            $('#step-2-badge')
                .removeClass('badge-primary badge-success badge-light border')
                .addClass(onStep2 ? 'badge-primary' : 'badge-light border');

            $('#step-1-label').css('color', onStep2 ? '#28a745' : '');
            $('#step-2-label').css('color', onStep2 ? '' : '#6c757d');

            if (onStep2) {
                // Re-init custom file inputs (dynamic E/F rows may have been added)
                if (typeof bsCustomFileInput !== 'undefined') {
                    bsCustomFileInput.destroy();
                    bsCustomFileInput.init();
                }
            }
        }

        // ── Dynamic Option E / F ────────────────────────────────────────────
        function addOption() {
            if (visibleOptionCount >= maxOptions) return;

            visibleOptionCount++;
            const nextLabel = optionLabels[visibleOptionCount - 1];
            $('#option-row-' + nextLabel).removeClass('d-none');

            updateAddButton();
        }

        function removeOption(label) {
            // Only allow removing E and F
            if (label !== 'E' && label !== 'F') return;

            // Remove F too if E is removed while F is visible (options must be contiguous)
            if (label === 'E' && visibleOptionCount === 6) {
                hideOptionRow('F');
            }
            hideOptionRow(label);
        }

        function hideOptionRow(label) {
            const row = $('#option-row-' + label);

            // Clear inputs
            row.find('input[type="text"]').val('');
            row.find('input[type="file"]').val('');
            row.find('.custom-file-label').text('Choose image');
            row.find('.option-preview-img').hide().attr('src', '#');
            row.find('input[type="radio"]').prop('checked', false);

            row.addClass('d-none');
            visibleOptionCount--;

            updateAddButton();
        }

        function updateAddButton() {
            if (visibleOptionCount >= maxOptions) {
                $('#add-option-btn-wrap').hide();
            } else {
                $('#add-option-btn-wrap').show();
                $('#add-option-text').text('Add Option ' + optionLabels[visibleOptionCount]);
            }
        }

        // ── Image previews ──────────────────────────────────────────────────
        function previewImage(input, containerId) {
            const preview = document.getElementById('image-preview');
            const container = document.getElementById(containerId);

            if (input.files && input.files[0]) {
                const reader = new FileReader();

                reader.onload = function (e) {
                    preview.src = e.target.result;
                    container.style.display = 'block';
                }

                reader.readAsDataURL(input.files[0]);
            } else {
                container.style.display = 'none';
            }
        }

        function previewOptionImage(input, previewId) {
            const preview = document.getElementById(previewId).querySelector('img');

            if (input.files && input.files[0]) {
                const reader = new FileReader();

                reader.onload = function (e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                }

                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
@endsection
