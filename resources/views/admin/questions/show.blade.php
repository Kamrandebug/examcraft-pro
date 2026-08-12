@extends('admin.layouts.master')

@section('title', 'View Question')
@section('page_title', 'View Question')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.questions.index') }}">Question Bank</a></li>
    <li class="breadcrumb-item active">View</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Question Details</h3>
                <div class="card-tools">
                    <a href="{{ route('admin.questions.edit', $question->id) }}" class="btn btn-warning btn-sm">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <a href="{{ route('admin.questions.index') }}" class="btn btn-default btn-sm">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label><strong>Question Text</strong></label>
                    <p>{{ $question->question_text }}</p>
                </div>

                @if($question->question_image)
                <div class="form-group">
                    <label><strong>Question Image</strong></label>
                    <div>
                        <img src="{{ asset('storage/' . $question->question_image) }}" class="img-fluid rounded border" style="max-height: 300px;">
                    </div>
                </div>
                @endif

                <hr>

                <h5>Options</h5>
                @if($question->options && $question->options->count() > 0)
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Label</th>
                                <th>Text</th>
                                <th>Image</th>
                                <th>Correct?</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($question->options as $option)
                            <tr class="{{ $option->label == $option->correct_option ? 'table-success' : '' }}">
                                <td><strong>{{ $option->label }}</strong></td>
                                <td>{{ $option->option_text }}</td>
                                <td>
                                    @if($option->option_image_url)
                                        <img src="{{ asset('storage/' . $option->option_image_url) }}" class="img-thumbnail" style="max-height: 60px;">
                                    @else
                                        —
                                    @endif
                                </td>
                                <td>
                                    @if($option->label == $option->correct_option)
                                        <span class="badge badge-success">Correct</span>
                                    @else
                                        —
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p class="text-muted">No options found for this question.</p>
                @endif

                <hr>

                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label><strong>Grade</strong></label>
                            <p>{{ $question->grade ?? '—' }}</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label><strong>Subject</strong></label>
                            <p>{{ $question->subject ?? '—' }}</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label><strong>Topic</strong></label>
                            <p>{{ $question->topic ?? '—' }}</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label><strong>Marks</strong></label>
                            <p>{{ $question->marks ?? '—' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
