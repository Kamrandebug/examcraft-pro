@extends('admin.layouts.master')

@section('title', 'Add Question')
@section('page_title', 'Add Question')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.questions.index') }}">Question Bank</a></li>
    <li class="breadcrumb-item active">Add Question</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8 offset-md-2">
        <form action="{{ route('admin.questions.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">New Question Details</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="question_text">Question Text</label>
                        <textarea name="question_text" id="question_text" class="form-control" rows="5" placeholder="Enter question text here...">{{ old('question_text') }}</textarea>
                        <small id="char-count" class="text-muted">0 characters</small>
                    </div>

                    <div class="form-group">
                        <label for="question_image">Question Image (Optional if text is provided)</label>
                        <div class="input-group">
                            <div class="custom-file">
                                <input type="file" name="question_image" class="custom-file-input" id="question_image" onchange="previewImage(this, 'preview-container')">
                                <label class="custom-file-label" for="question_image">Choose image</label>
                            </div>
                        </div>
                        <div id="preview-container" class="mt-3" style="display: none;">
                            <img id="image-preview" src="#" alt="Preview" class="img-fluid rounded border" style="max-height: 250px;">
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Save Question</button>
                    <a href="{{ route('admin.questions.index') }}" class="btn btn-default float-right">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(function () {
        // Character count logic
        $('#question_text').on('input', function () {
            let count = $(this).val().length;
            $('#char-count').text(count + ' characters');
        });

        // Initialize custom file input
        if (typeof bsCustomFileInput !== 'undefined') {
            bsCustomFileInput.init();
        }
    });

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
</script>
@endsection
