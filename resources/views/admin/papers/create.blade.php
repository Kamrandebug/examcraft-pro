@extends('admin.layouts.master')

@section('title', 'Create Paper')
@section('page_title', 'Create Exam Paper')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.papers.index') }}">Exam Papers</a></li>
    <li class="breadcrumb-item active">Create</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8 offset-md-2">
        <form action="{{ route('admin.papers.store') }}" method="POST">
            @csrf

            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">New Exam Paper</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="title">Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}" required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="subject">Subject</label>
                                <input type="text" name="subject" id="subject" class="form-control" value="{{ old('subject') }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="exam_code">Exam Code</label>
                                <input type="text" name="exam_code" id="exam_code" class="form-control" value="{{ old('exam_code') }}" placeholder="e.g. 9702/12">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="organization">Organization</label>
                                <input type="text" name="organization" id="organization" class="form-control" value="{{ old('organization') }}" placeholder="e.g. Cambridge International">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="session">Session</label>
                                <input type="text" name="session" id="session" class="form-control" value="{{ old('session') }}" placeholder="e.g. May/June">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="year">Year</label>
                                <input type="number" name="year" id="year" class="form-control" value="{{ old('year') }}" placeholder="e.g. 2026">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="duration">Duration</label>
                                <input type="text" name="duration" id="duration" class="form-control" value="{{ old('duration') }}" placeholder="e.g. 1 hour 15 minutes">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="typography_preset">Typography Preset</label>
                                <input type="text" name="typography_preset" id="typography_preset" class="form-control" value="{{ old('typography_preset') }}">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="instructions">Instructions</label>
                        <textarea name="instructions" id="instructions" class="form-control" rows="3">{{ old('instructions') }}</textarea>
                    </div>

                    <div class="form-group">
                        <label for="materials">Materials</label>
                        <textarea name="materials" id="materials" class="form-control" rows="3" placeholder="List required materials...">{{ old('materials') }}</textarea>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="{{ route('admin.papers.index') }}" class="btn btn-default">Cancel</a>
                    <button type="submit" class="btn btn-primary float-right">Create Paper</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
