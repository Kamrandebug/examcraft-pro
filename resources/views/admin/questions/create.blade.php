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
                    {{-- Grade + Subject + Marks --}}
                    <div class="form-group row">
                        <label for="grade" class="col-sm-2 col-form-label">Grade <span class="text-danger">*</span></label>
                        <div class="col-sm-3">
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
                        <div class="col-sm-3">
                            <select name="subject" id="subject" class="form-control @error('subject') is-invalid @enderror" required disabled>
                                <option value="">— Select Grade First —</option>
                            </select>
                            @error('subject')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <label for="marks" class="col-sm-1 col-form-label">Marks</label>
                        <div class="col-sm-1">
                            <input type="number" name="marks" id="marks" class="form-control" min="0" value="{{ old('marks', 1) }}">
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

                    {{-- Options A-D + correct answer --}}
                    <h5>Answer Options</h5>
                    @foreach(['A', 'B', 'C', 'D'] as $opt)
                        @php $lower = strtolower($opt); @endphp
                        <div class="card card-light border mb-3">
                            <div class="card-body py-2">
                                <div class="row align-items-end">
                                    <div class="col-sm-2">
                                        <span class="badge badge-primary">Option {{ $opt }}</span>
                                    </div>
                                    <div class="col-sm-6">
                                        <label for="option_{{ $lower }}_text" class="mb-0">Text</label>
                                        <input type="text" name="option_{{ $lower }}_text" id="option_{{ $lower }}_text" class="form-control @error('option_'.$lower.'_text') is-invalid @enderror" placeholder="Enter option {{ $opt }} text" value="{{ old('option_'.$lower.'_text') }}">
                                        @error('option_'.$lower.'_text')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-sm-4">
                                        <label for="option_{{ $lower }}_image" class="mb-0">Image (optional)</label>
                                        <div class="custom-file">
                                            <input type="file" name="option_{{ $lower }}_image" class="custom-file-input" id="option_{{ $lower }}_image" onchange="previewOptionImage(this, 'option-preview-{{ $opt }}')">
                                            <label class="custom-file-label" for="option_{{ $lower }}_image">Choose image</label>
                                        </div>
                                        <div id="option-preview-{{ $opt }}" class="mt-2">
                                            <img src="#" class="option-preview-img img-thumbnail" alt="Option {{ $opt }} Preview">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach

                    <div class="form-group">
                        <label>Correct Answer <span class="text-danger">*</span></label>
                        <div class="d-flex">
                            @foreach(['A', 'B', 'C', 'D'] as $opt)
                                <div class="icheck-success d-inline mr-4">
                                    <input type="radio" name="correct_answer" value="{{ $opt }}" id="correct_{{ $opt }}" {{ old('correct_answer') == $opt ? 'checked' : '' }}>
                                    <label for="correct_{{ $opt }}">{{ $opt }}</label>
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

        function previewOptionImage(input, previewId) {
            const preview = document.getElementById(previewId).querySelector('img');

            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
@endsection
