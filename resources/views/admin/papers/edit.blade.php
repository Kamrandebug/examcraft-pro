@extends('admin.layouts.master')

@section('title', 'Edit Paper')
@section('page_title', 'Edit Exam Paper')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.papers.index') }}">Exam Papers</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8 offset-md-2">
        <form action="{{ route('admin.papers.update', $paper->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Edit: {{ $paper->title }}</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="title">Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $paper->title) }}" required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="subject">Subject</label>
                                <input type="text" name="subject" id="subject" class="form-control" value="{{ old('subject', $paper->subject) }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="exam_code">Exam Code</label>
                                <input type="text" name="exam_code" id="exam_code" class="form-control" value="{{ old('exam_code', $paper->exam_code) }}">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="organization">Organization</label>
                                <input type="text" name="organization" id="organization" class="form-control" value="{{ old('organization', $paper->organization) }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="session">Session</label>
                                <input type="text" name="session" id="session" class="form-control" value="{{ old('session', $paper->session) }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="year">Year</label>
                                <input type="number" name="year" id="year" class="form-control" value="{{ old('year', $paper->year) }}">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="duration">Duration</label>
                                <input type="text" name="duration" id="duration" class="form-control" value="{{ old('duration', $paper->duration) }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="status">Status</label>
                                <select name="status" id="status" class="form-control">
                                    <option value="draft" {{ old('status', $paper->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                                    <option value="published" {{ old('status', $paper->status) == 'published' ? 'selected' : '' }}>Published</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="typography_preset">Typography Preset</label>
                        <input type="text" name="typography_preset" id="typography_preset" class="form-control" value="{{ old('typography_preset', $paper->typography_preset) }}">
                    </div>

                    <div class="form-group">
                        <label for="instructions">Instructions</label>
                        <textarea name="instructions" id="instructions" class="form-control" rows="3">{{ old('instructions', $paper->instructions) }}</textarea>
                    </div>

                    <div class="form-group">
                        <label for="materials">Materials</label>
                        <textarea name="materials" id="materials" class="form-control" rows="3">{{ old('materials', $paper->materials) }}</textarea>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="{{ route('admin.papers.show', $paper->id) }}" class="btn btn-default">Cancel</a>
                    <button type="submit" class="btn btn-primary float-right">Update Paper</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
