@extends('user.layouts.app')

@section('title', 'Edit Paper')
@section('page_title', 'Edit Paper')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('user.papers.index') }}">My Papers</a></li>
    <li class="breadcrumb-item"><a href="{{ route('user.papers.show', $paper->id) }}">{{ Str::limit($paper->title, 25) }}</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('styles')
    <style>
        .edit-paper-sheet {
            background: #fff;
            border: 1px solid #e2e2e2;
            border-radius: 4px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .edit-paper-sheet .q-item {
            padding: 8px 12px;
            border: 1px solid #eee;
            border-radius: 4px;
            margin-bottom: 8px;
            background: #fafafa;
        }
        .edit-paper-sheet .opt-line {
            margin-left: 24px;
            font-size: 13px;
            color: #555;
        }
        .edit-paper-sheet img.q-img {
            max-height: 120px;
            display: block;
            margin: 6px 0;
        }
        .edit-paper-sheet img.opt-img {
            max-height: 60px;
            display: block;
            margin: 4px 0 4px 24px;
        }
    </style>
@endsection

@section('content')
@php
    $pd = $paper->paper_data ?? [];
    $isAuto = $paper->type === 'auto';
    $mcqs = $pd['selectedMcqs'] ?? [];
@endphp

<div class="row justify-content-center">
    <div class="col-lg-10">
        <form method="POST" action="{{ route('user.papers.update', $paper->id) }}">
            @csrf @method('PUT')

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">Edit Paper</h3>
                    <span class="badge badge-{{ $paper->type === 'auto' ? 'auto' : 'manual' }}">
                        {{ ucfirst($paper->type) }} Paper
                    </span>
                </div>

                <div class="card-body">
                    {{-- Common metadata --}}
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Paper Title <span class="text-danger">*</span></label>
                                <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                                       value="{{ old('title', $paper->title) }}" required>
                                @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Status <span class="text-danger">*</span></label>
                                <select name="status" class="form-control">
                                    <option value="draft" {{ old('status', $paper->status) === 'draft' ? 'selected' : '' }}>Draft</option>
                                    <option value="published" {{ old('status', $paper->status) === 'published' ? 'selected' : '' }}>Published</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Exam Date</label>
                                <input type="date" name="exam_date" class="form-control"
                                       value="{{ old('exam_date', $paper->exam_date?->format('Y-m-d')) }}">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Subject</label>
                                <input type="text" name="subject" class="form-control"
                                       value="{{ old('subject', $paper->subject) }}" placeholder="e.g. Physics, Chemistry">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Grade / Class</label>
                                <input type="text" name="grade" class="form-control"
                                       value="{{ old('grade', $paper->grade) }}" placeholder="e.g. O Level">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>School / Institution</label>
                                <input type="text" name="school_name" class="form-control"
                                       value="{{ old('school_name', $paper->school_name) }}" placeholder="School or institution name">
                            </div>
                        </div>
                    </div>

                    @if($isAuto)
                        <hr>
                        <h6 class="font-weight-bold mb-3">Paper Identity</h6>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Paper Code</label>
                                    <input type="text" name="paper_code" class="form-control"
                                           value="{{ old('paper_code', $pd['paperCode'] ?? '') }}" placeholder="e.g. 5054/11">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Session</label>
                                    <input type="text" name="session" class="form-control"
                                           value="{{ old('session', $pd['session'] ?? '') }}" placeholder="e.g. May/June 2025">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Duration</label>
                                    <input type="text" name="duration" class="form-control"
                                           value="{{ old('duration', $pd['duration'] ?? '') }}" placeholder="e.g. 1 hour">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Additional Materials (one per line)</label>
                            <textarea name="additional_materials" class="form-control" rows="3">{{ old('additional_materials', $pd['additionalMaterials'] ?? '') }}</textarea>
                        </div>

                        <div class="form-group">
                            <label>Instructions</label>
                            <textarea name="instructions" class="form-control" rows="8">{{ old('instructions', $pd['instructions'] ?? '') }}</textarea>
                        </div>

                        @if(!empty($pd['logoDataUrl']))
                            <div class="form-group">
                                <label>Current Logo</label>
                                <div>
                                    <img src="{{ $pd['logoDataUrl'] }}" alt="Logo" style="max-height:60px; border:1px solid #ddd; padding:2px;">
                                </div>
                            </div>
                        @endif
                    @endif
                </div>

                <div class="card-footer d-flex justify-content-between">
                    <a href="{{ route('user.papers.show', $paper->id) }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left mr-1"></i>Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i>Save Changes
                    </button>
                </div>
            </div>
        </form>

        {{-- Read-only preview of the questions included in this paper --}}
        @if($isAuto && count($mcqs))
        <div class="card">
            <div class="card-header">
                <h3 class="card-title mb-0">Questions in this paper ({{ count($mcqs) }})</h3>
            </div>
            <div class="card-body">
                <div class="edit-paper-sheet">
                    @foreach($mcqs as $index => $mcq)
                        <div class="q-item">
                            <p class="mb-1"><strong>{{ $index + 1 }}.</strong> {{ $mcq['stem_text'] ?? '' }}</p>
                            @if(!empty($mcq['stem_image']))
                                <img src="{{ $mcq['stem_image'] }}" class="q-img" alt="Question image">
                            @endif
                            @foreach(($mcq['options'] ?? []) as $opt)
                                <div class="opt-line">
                                    <strong>{{ $opt['label'] }}</strong> {{ $opt['text'] ?? '' }}
                                    @if(!empty($opt['image']))
                                        <img src="{{ $opt['image'] }}" class="opt-img" alt="Option image">
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        @elseif($isAuto)
        <div class="callout callout-info">
            <h6>No questions saved in this paper.</h6>
            <p class="mb-0 text-muted">You can add questions from the Auto Paper Generator.</p>
        </div>
        @endif
    </div>
</div>
@endsection
