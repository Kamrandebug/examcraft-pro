@extends('admin.layouts.master')

@section('title', 'Manage Question Options')
@section('page_title', 'Question Options')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.questions.index') }}">Question Bank</a></li>
    <li class="breadcrumb-item active">Manage Options</li>
@endsection

@section('styles')
  <style>
    .question-preview-img {
        max-height: 100px;
        margin-bottom: 10px;
    }
    .option-preview-img {
        max-height: 60px;
        margin-top: 5px;
        display: block;
    }
    .remove-option-btn {
        position: absolute;
        right: 10px;
        top: 10px;
        color: #dc3545;
        cursor: pointer;
    }
    .option-card {
        position: relative;
    }
  </style>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        @foreach($questions as $question)
        <div class="card card-outline card-info mb-4" id="question-card-{{ $question->id }}">
            <div class="card-header">
                <h3 class="card-title">
                    <strong>Question #{{ $loop->iteration }}</strong>
                </h3>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-12">
                        @if($question->question_image)
                            <img src="{{ asset('storage/' . $question->question_image) }}" class="question-preview-img img-fluid border rounded" alt="Question Image">
                        @endif
                        <p class="lead">{{ $question->question_text }}</p>
                    </div>
                </div>

                <form action="{{ route('admin.questions.options.store') }}" method="POST" enctype="multipart/form-data" class="option-form">
                    @csrf
                    <input type="hidden" name="question_id" value="{{ $question->id }}">
                    
                    <div class="mb-3">
                        <label>Correct Answer:</label>
                        <div id="correct-radios-{{ $question->id }}" class="d-flex flex-wrap">
                            {{-- Radios will be injected here by JS --}}
                        </div>
                    </div>

                    <div class="row" id="options-container-{{ $question->id }}">
                        @php
                            $options = $question->options->first();
                            $existingOptionsCount = 4;
                            if ($options) {
                                if ($options->option_f_text || $options->option_f_image) $existingOptionsCount = 6;
                                elseif ($options->option_e_text || $options->option_e_image) $existingOptionsCount = 5;
                            }
                        @endphp
                        
                        @foreach(['a', 'b', 'c', 'd', 'e', 'f'] as $opt)
                            @php
                                $label = strtoupper($opt);
                                $isVisible = ($opt <= 'd' || ($options && ($options->{"option_{$opt}_text"} || $options->{"option_{$opt}_image"})));
                            @endphp
                            <div class="col-md-6 mb-3 option-row {{ $isVisible ? '' : 'd-none' }}" data-label="{{ $label }}" id="option-row-{{ $question->id }}-{{ $label }}">
                                <div class="card card-light border option-card">
                                    @if($opt > 'd')
                                        <span class="remove-option-btn" onclick="removeOption({{ $question->id }}, '{{ $label }}')">
                                            <i class="fas fa-times-circle"></i> Remove
                                        </span>
                                    @endif
                                    <div class="card-body">
                                        <div class="d-flex align-items-center mb-2">
                                            <span class="badge badge-primary mr-2">Option {{ $label }}</span>
                                        </div>
                                        
                                        <div class="form-group">
                                            <input type="text" name="option_{{ $opt }}_text" class="form-control" placeholder="Enter option text" value="{{ $options ? $options->{"option_{$opt}_text"} : '' }}">
                                        </div>
                                        
                                        <div class="form-group mb-0">
                                            <div class="input-group">
                                                <div class="custom-file">
                                                    <input type="file" name="option_{{ $opt }}_image" class="custom-file-input" onchange="previewOptionImage(this, 'preview_{{ $question->id }}_{{ $opt }}')">
                                                    <label class="custom-file-label">Choose image</label>
                                                </div>
                                            </div>
                                            <div id="preview_{{ $question->id }}_{{ $opt }}" class="mt-2">
                                                @if($options && $options->{"option_{$opt}_image"})
                                                    <img src="{{ asset('storage/' . $options->{"option_{$opt}_image"}) }}" class="option-preview-img img-thumbnail">
                                                @else
                                                    <img src="#" class="option-preview-img img-thumbnail" style="display: none;">
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    <div class="row">
                        <div class="col-12">
                            <button type="button" class="btn btn-outline-success btn-sm mb-3" id="add-option-btn-{{ $question->id }}" onclick="addOption({{ $question->id }})">
                                <i class="fas fa-plus"></i> Add Option
                            </button>
                        </div>
                    </div>
                    
                    <div class="text-right mt-2 border-top pt-3">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> Save Options for Question #{{ $loop->iteration }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
        @endforeach

        @if($questions->isEmpty())
            <div class="alert alert-info text-center">
                <h5><i class="icon fas fa-info"></i> No questions found!</h5>
                Please add some questions first.
            </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
    const optionLabels = ['A', 'B', 'C', 'D', 'E', 'F'];
    const maxOptions = 6;
    let questionOptionCounts = {};
    let initialCorrectOptions = {};

    $(function () {
        @foreach($questions as $question)
            @php
                $opts = $question->options->first();
                $count = 4;
                if ($opts) {
                    if ($opts->option_f_text || $opts->option_f_image) $count = 6;
                    elseif ($opts->option_e_text || $opts->option_e_image) $count = 5;
                }
                $correct = $opts ? $opts->correct_option : 'A';
            @endphp
            questionOptionCounts[{{ $question->id }}] = {{ $count }};
            initialCorrectOptions[{{ $question->id }}] = '{{ $correct }}';
            updateCorrectRadios({{ $question->id }}, {{ $count }}, '{{ $correct }}');
            checkAddButton({{ $question->id }});
        @endforeach

        // Initialize custom file input
        if (typeof bsCustomFileInput !== 'undefined') {
            bsCustomFileInput.init();
        }
    });

    function addOption(questionId) {
        let count = questionOptionCounts[questionId];
        if (count >= maxOptions) return;

        count++;
        questionOptionCounts[questionId] = count;
        
        const nextLabel = optionLabels[count - 1];
        $(`#option-row-${questionId}-${nextLabel}`).removeClass('d-none');
        
        updateCorrectRadios(questionId, count);
        checkAddButton(questionId);
    }

    function removeOption(questionId, label) {
        // Only allow removing E and F
        if (label !== 'E' && label !== 'F') return;

        const rowIndex = optionLabels.indexOf(label);
        const count = rowIndex; // If we remove E (index 4), count becomes 4 (A,B,C,D)
        
        // Hide the row and its successor if it's F
        $(`#option-row-${questionId}-${label}`).addClass('d-none');
        // Clear inputs
        $(`#option-row-${questionId}-${label} input[type="text"]`).val('');
        $(`#option-row-${questionId}-${label} input[type="file"]`).val('');
        $(`#option-row-${questionId}-${label} .custom-file-label`).text('Choose image');
        $(`#option-row-${questionId}-${label} .option-preview-img`).hide().attr('src', '#');

        // If we remove E but F is visible, we should probably handle that too, 
        // but the UI only allows adding in order.
        if (label === 'E' && questionOptionCounts[questionId] === 6) {
            removeOption(questionId, 'F');
        }

        questionOptionCounts[questionId] = count;
        updateCorrectRadios(questionId, count);
        checkAddButton(questionId);
    }

    function updateCorrectRadios(questionId, count, selected = null) {
        const container = $(`#correct-radios-${questionId}`);
        const currentSelected = selected || container.find('input:checked').val() || 'A';
        
        container.empty();
        for (let i = 0; i < count; i++) {
            const label = optionLabels[i];
            const isChecked = currentSelected === label ? 'checked' : '';
            
            container.append(`
                <div class="icheck-success d-inline mr-3 mb-2">
                    <input type="radio" name="correct_option" value="${label}" id="correct_${questionId}_${label}" ${isChecked} required>
                    <label for="correct_${questionId}_${label}">${label}</label>
                </div>
            `);
        }
    }

    function checkAddButton(questionId) {
        const btn = $(`#add-option-btn-${questionId}`);
        if (questionOptionCounts[questionId] >= maxOptions) {
            btn.hide();
        } else {
            btn.show();
        }
    }

    function previewOptionImage(input, previewId) {
        const container = document.getElementById(previewId);
        const preview = container.querySelector('img');
        
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
