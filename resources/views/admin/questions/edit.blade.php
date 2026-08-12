@extends('admin.layouts.master')

@section('title', 'Edit Question')
@section('page_title', 'Edit Question')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.questions.index') }}">Question Bank</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
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
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <label for="subject" class="col-sm-2 col-form-label">Subject <span class="text-danger">*</span></label>
                        <div class="col-sm-4">
                            <select name="subject" id="subject" class="form-control @error('subject') is-invalid @enderror" required>
                                <option value="">— Select Subject —</option>
                            </select>
                            @error('subject')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="question_text">Question Text <span class="text-danger">*</span></label>
                        <textarea name="question_text" id="question_text" class="form-control @error('question_text') is-invalid @enderror" rows="4" required>{{ old('question_text', $question->question_text) }}</textarea>
                        @error('question_text')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="topic">Topic</label>
                                <input type="text" name="topic" id="topic" class="form-control" value="{{ old('topic', $question->topic) }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="difficulty">Difficulty</label>
                                <select name="difficulty" id="difficulty" class="form-control">
                                    <option value="">— Select —</option>
                                    <option value="easy" {{ old('difficulty', $question->difficulty) == 'easy' ? 'selected' : '' }}>Easy</option>
                                    <option value="medium" {{ old('difficulty', $question->difficulty) == 'medium' ? 'selected' : '' }}>Medium</option>
                                    <option value="hard" {{ old('difficulty', $question->difficulty) == 'hard' ? 'selected' : '' }}>Hard</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="marks">Marks</label>
                                <input type="number" name="marks" id="marks" class="form-control" min="1" value="{{ old('marks', $question->marks) }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="option_type">Type</label>
                                <select name="option_type" id="option_type" class="form-control">
                                    <option value="">— Select —</option>
                                    <option value="MCQ" {{ old('option_type', $question->option_type) == 'MCQ' ? 'selected' : '' }}>MCQ</option>
                                    <option value="Text" {{ old('option_type', $question->option_type) == 'Text' ? 'selected' : '' }}>Text</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="correct_answer">Correct Answer</label>
                                <input type="text" name="correct_answer" id="correct_answer" class="form-control" maxlength="10" value="{{ old('correct_answer', $question->correct_answer) }}">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="question_image">Question Image</label>
                                <div class="input-group">
                                    <div class="custom-file">
                                        <input type="file" name="question_image" class="custom-file-input @error('question_image') is-invalid @enderror" id="question_image" onchange="previewEditImage(this)">
                                        <label class="custom-file-label" for="question_image">Choose image</label>
                                    </div>
                                </div>
                                @error('question_image')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                @if($question->question_image)
                                    <div id="current-image" class="mt-2">
                                        <p class="text-muted small mb-1">Current image:</p>
                                        <img src="{{ asset('storage/' . $question->question_image) }}" class="img-fluid rounded border" style="max-height: 200px;">
                                    </div>
                                @endif
                                <div id="edit-preview-container" class="mt-2" style="display: none;">
                                    <p class="text-muted small mb-1">New image preview:</p>
                                    <img id="edit-image-preview" src="#" class="img-fluid rounded border" style="max-height: 200px;">
                                </div>
                            </div>
                        </div>
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

    function previewEditImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#edit-image-preview').attr('src', e.target.result);
                $('#edit-preview-container').show();
                $('#current-image').hide();
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endsection
