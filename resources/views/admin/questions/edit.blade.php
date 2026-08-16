@extends('admin.layouts.master')

@section('title', 'Edit Question')
@section('page_title', 'Edit Question')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.questions.index') }}">Question Bank</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('styles')
    <!-- icheck-bootstrap -->
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">
    <style>
        .option-preview-img {
            max-height: 60px;
            margin-top: 5px;
        }
    </style>
@endsection

@section('content')
@php
    $data = $question->data ?? [];
    $options = $data['options'] ?? [];
    $optionsByLabel = [];
    foreach ($options as $opt) {
        $optionsByLabel[$opt['label'] ?? ''] = $opt;
    }
@endphp

<div class="row">
    <div class="col-md-8 offset-md-2">
        <form action="{{ route('admin.questions.update', $question->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Edit Question</h3>
                </div>
                <div class="card-body">
                    {{-- Grade + Subject + Marks --}}
                    <div class="form-group row">
                        <label for="grade" class="col-sm-2 col-form-label">Grade <span class="text-danger">*</span></label>
                        <div class="col-sm-3">
                            <select name="grade" id="grade" class="form-control @error('grade') is-invalid @enderror" required>
                                <option value="">— Select Grade —</option>
                                @foreach(['O Level','A Level','8th Grade','9th Grade','10th Grade'] as $g)
                                    <option value="{{ $g }}" {{ old('grade', $question->grade) == $g ? 'selected' : '' }}>{{ $g }}</option>
                                @endforeach
                            </select>
                            @error('grade')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <label for="subject" class="col-sm-2 col-form-label">Subject <span class="text-danger">*</span></label>
                        <div class="col-sm-3">
                            <select name="subject" id="subject" class="form-control @error('subject') is-invalid @enderror" required>
                                <option value="">— Select Subject —</option>
                            </select>
                            @error('subject')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <label for="marks" class="col-sm-1 col-form-label">Marks</label>
                        <div class="col-sm-1">
                            <input type="number" name="marks" id="marks" class="form-control" min="0" value="{{ old('marks', $question->marks) }}">
                        </div>
                    </div>

                    {{-- Stem text + image --}}
                    <div class="form-group">
                        <label for="stem_text">Question Text <span class="text-danger">*</span></label>
                        <textarea name="stem_text" id="stem_text" class="form-control @error('stem_text') is-invalid @enderror" rows="4">{{ old('stem_text', $data['stem_text'] ?? '') }}</textarea>
                        @error('stem_text')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="stem_image">Question Image (optional)</label>
                        <div class="input-group">
                            <div class="custom-file">
                                <input type="file" name="stem_image" class="custom-file-input @error('stem_image') is-invalid @enderror" id="stem_image" onchange="previewStemImage(this)">
                                <label class="custom-file-label" for="stem_image">Choose image</label>
                            </div>
                        </div>
                        @error('stem_image')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        @if(!empty($data['stem_image']))
                            <div id="current-stem-image" class="mt-2">
                                <p class="text-muted small mb-1">Current image:</p>
                                <img src="{{ Storage::url($data['stem_image']) }}" class="img-fluid rounded border" style="max-height: 200px;">
                            </div>
                        @endif
                        <div id="stem-preview-container" class="mt-2" style="display: none;">
                            <p class="text-muted small mb-1">New image preview:</p>
                            <img id="stem-image-preview" src="#" class="img-fluid rounded border" style="max-height: 200px;">
                        </div>
                    </div>

                    <hr>

                    {{-- Options A-D + correct answer --}}
                    <h5>Answer Options</h5>
                    @foreach(['A', 'B', 'C', 'D'] as $opt)
                        @php
                            $lower = strtolower($opt);
                            $current = $optionsByLabel[$opt] ?? [];
                        @endphp
                        <div class="card card-light border mb-3">
                            <div class="card-body py-2">
                                <div class="row align-items-end">
                                    <div class="col-sm-2">
                                        <span class="badge badge-primary">Option {{ $opt }}</span>
                                    </div>
                                    <div class="col-sm-6">
                                        <label for="option_{{ $lower }}_text" class="mb-0">Text</label>
                                        <input type="text" name="option_{{ $lower }}_text" id="option_{{ $lower }}_text" class="form-control" placeholder="Enter option {{ $opt }} text" value="{{ old('option_'.$lower.'_text', $current['text'] ?? '') }}">
                                    </div>
                                    <div class="col-sm-4">
                                        <label for="option_{{ $lower }}_image" class="mb-0">Image (optional)</label>
                                        <div class="custom-file">
                                            <input type="file" name="option_{{ $lower }}_image" class="custom-file-input" id="option_{{ $lower }}_image" onchange="previewOptionImage(this, 'option-preview-{{ $opt }}')">
                                            <label class="custom-file-label" for="option_{{ $lower }}_image">Choose image</label>
                                        </div>
                                        @if(!empty($current['image']))
                                            <div class="mt-2">
                                                <img src="{{ Storage::url($current['image']) }}" class="img-thumbnail" style="max-height: 60px;">
                                            </div>
                                        @endif
                                        <div id="option-preview-{{ $opt }}" class="mt-2">
                                            <img src="#" class="option-preview-img img-thumbnail" alt="Option {{ $opt }} Preview" style="display: none;">
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
                                    <input type="radio" name="correct_answer" value="{{ $opt }}" id="correct_{{ $opt }}" {{ (old('correct_answer', $data['correct_answer'] ?? '') == $opt) ? 'checked' : '' }}>
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
                    <a href="{{ route('admin.questions.show', $question->id) }}" class="btn btn-default">Cancel</a>
                    <button type="submit" class="btn btn-primary float-right">Update Question</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('adminlte/plugins/bs-custom-file-input/bs-custom-file-input.min.js') }}"></script>
<script>
    $(function () {
        if (typeof bsCustomFileInput !== 'undefined') {
            bsCustomFileInput.init();
        }

        const gradeSubjects = {
            'O Level':   ['Physics','Chemistry','Biology','Mathematics','Computer Science','English Language','Urdu','Islamiyat','Pakistan Studies','Economics','Commerce','Accounting'],
            'A Level':   ['Physics','Chemistry','Biology','Mathematics','Further Mathematics','Computer Science','Economics','Psychology'],
            '8th Grade': ['General Science','Mathematics','Urdu','English','Social Studies','Islamiyat','Pakistan Studies'],
            '9th Grade': ['Physics','Chemistry','Biology','Mathematics','Computer Science','Urdu','English','Islamiyat','Pakistan Studies'],
            '10th Grade':['Physics','Chemistry','Biology','Mathematics','Computer Science','Urdu','English','Islamiyat','Pakistan Studies'],
        };

        const $gradeE   = $('#grade');
        const $subjectE = $('#subject');
        const currentGrade   = '{!! addslashes(old('grade', $question->grade ?? '')) !!}';
        const currentSubject = '{!! addslashes(old('subject', $question->subject ?? '')) !!}';

        function populateEditSubjects(grade, preselect) {
            $subjectE.empty().append('<option value="">— Select Subject —</option>');
            if (!grade || !gradeSubjects[grade]) {
                $subjectE.prop('disabled', true);
                return;
            }
            gradeSubjects[grade].forEach(function (sub) {
                const selected = (sub === preselect) ? ' selected' : '';
                $subjectE.append('<option value="' + sub + '"' + selected + '>' + sub + '</option>');
            });
            $subjectE.prop('disabled', false);
        }

        populateEditSubjects(currentGrade, currentSubject);

        $gradeE.on('change', function () {
            populateEditSubjects($(this).val(), '');
        });
    });

    function previewStemImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#stem-image-preview').attr('src', e.target.result);
                $('#stem-preview-container').show();
                $('#current-stem-image').hide();
            };
            reader.readAsDataURL(input.files[0]);
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
