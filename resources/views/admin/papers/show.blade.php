@extends('admin.layouts.master')

@section('title', 'View Paper')
@section('page_title', 'View Exam Paper')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.papers.index') }}">Exam Papers</a></li>
    <li class="breadcrumb-item active">View</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">{{ $paper->title }}</h3>
                <div class="card-tools">
                    <a href="{{ route('admin.papers.edit', $paper->id) }}" class="btn btn-warning btn-sm">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <a href="{{ route('admin.papers.index') }}" class="btn btn-default btn-sm">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label><strong>Subject</strong></label>
                            <p>{{ $paper->subject ?? '—' }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label><strong>Exam Code</strong></label>
                            <p>{{ $paper->exam_code ?? '—' }}</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label><strong>Organization</strong></label>
                            <p>{{ $paper->organization ?? '—' }}</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label><strong>Session / Year</strong></label>
                            <p>{{ $paper->session ?? '—' }} {{ $paper->year ?? '—' }}</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label><strong>Duration</strong></label>
                            <p>{{ $paper->duration ?? '—' }}</p>
                        </div>
                    </div>
                </div>

                <hr>

                <div class="form-group">
                    <label><strong>Status</strong></label>
                    <p>
                        <span class="badge badge-{{ $paper->status == 'published' ? 'success' : 'warning' }}">
                            {{ ucfirst($paper->status ?? 'Draft') }}
                        </span>
                    </p>
                </div>

                @if($paper->instructions)
                <div class="form-group">
                    <label><strong>Instructions</strong></label>
                    <p>{{ $paper->instructions }}</p>
                </div>
                @endif

                @if($paper->materials)
                <div class="form-group">
                    <label><strong>Materials</strong></label>
                    <p>{{ $paper->materials }}</p>
                </div>
                @endif

                @if($paper->typography_preset)
                <div class="form-group">
                    <label><strong>Typography Preset</strong></label>
                    <p>{{ $paper->typography_preset }}</p>
                </div>
                @endif

                <hr>

                <h5>Pages ({{ $paper->pages->count() }})</h5>
                @if($paper->pages->count() > 0)
                    <table class="table table-sm table-bordered">
                        <thead>
                            <tr>
                                <th>Page #</th>
                                <th>Label</th>
                                <th>Blocks</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($paper->pages as $page)
                            <tr>
                                <td>{{ $page->page_number }}</td>
                                <td>{{ $page->label ?? '—' }}</td>
                                <td>{{ $page->blocks->count() }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p class="text-muted">No pages yet.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
