@extends('admin.layouts.master')

@section('title', "Create Paper for {$user->name}")
@section('page_title', "Create Paper")

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Users</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.user.papers.index', $user->id) }}">{{ $user->name }}'s Papers</a></li>
    <li class="breadcrumb-item active">Create</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-primary">
                <h3 class="card-title">Create New Paper for {{ $user->name }}</h3>
            </div>
            <form action="{{ route('admin.user.papers.store', $user->id) }}" method="POST">
                @csrf
                <div class="card-body">
                    <div class="form-group">
                        <label for="type">Paper Type <span class="text-danger">*</span></label>
                        <select id="type" name="type" class="form-control @error('type') is-invalid @enderror" required>
                            <option value="">-- Select Type --</option>
                            <option value="manual" selected>Manual Paper (Designer)</option>
                            <option value="auto">Auto Paper (Wizard)</option>
                        </select>
                        @error('type')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="title">Title</label>
                        <input type="text" id="title" name="title" class="form-control @error('title') is-invalid @enderror"
                               placeholder="e.g., Physics Paper 1" value="{{ old('title', 'Untitled Paper') }}">
                        @error('title')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="subject">Subject</label>
                        <input type="text" id="subject" name="subject" class="form-control @error('subject') is-invalid @enderror"
                               placeholder="e.g., Physics" value="{{ old('subject') }}">
                        @error('subject')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="grade">Grade/Level</label>
                        <input type="text" id="grade" name="grade" class="form-control @error('grade') is-invalid @enderror"
                               placeholder="e.g., O Level" value="{{ old('grade') }}">
                        @error('grade')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="school_name">School/Organization</label>
                        <input type="text" id="school_name" name="school_name" class="form-control @error('school_name') is-invalid @enderror"
                               placeholder="e.g., Cambridge Assessment" value="{{ old('school_name') }}">
                        @error('school_name')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="exam_date">Exam Date</label>
                        <input type="date" id="exam_date" name="exam_date" class="form-control @error('exam_date') is-invalid @enderror"
                               value="{{ old('exam_date') }}">
                        @error('exam_date')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="card-footer">
                    <a href="{{ route('admin.user.papers.index', $user->id) }}" class="btn btn-secondary">
                        <i class="fas fa-times mr-1"></i> Cancel
                    </a>
                    <button type="submit" class="btn btn-primary float-right">
                        <i class="fas fa-check mr-1"></i> Create Paper
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
