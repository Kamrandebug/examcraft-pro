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
                                <label for="subject">Subject</label>
                                <input type="text" name="subject" id="subject" class="form-control" value="{{ old('subject', $question->subject) }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="topic">Topic</label>
                                <input type="text" name="topic" id="topic" class="form-control" value="{{ old('topic', $question->topic) }}">
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
                                <label for="difficulty">Difficulty</label>
                                <select name="difficulty" id="difficulty" class="form-control">
                                    <option value="">— Select —</option>
                                    <option value="easy" {{ old('difficulty', $question->difficulty) == 'easy' ? 'selected' : '' }}>Easy</option>
                                    <option value="medium" {{ old('difficulty', $question->difficulty) == 'medium' ? 'selected' : '' }}>Medium</option>
                                    <option value="hard" {{ old('difficulty', $question->difficulty) == 'hard' ? 'selected' : '' }}>Hard</option>
                                </select>
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
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="correct_answer">Correct Answer</label>
                                <input type="text" name="correct_answer" id="correct_answer" class="form-control" maxlength="10" value="{{ old('correct_answer', $question->correct_answer) }}">
                            </div>
                        </div>
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
