@extends('user.layouts.app')

@section('title', $paper->title)
@section('page_title', 'Paper Details')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('user.papers.index') }}">My Papers</a></li>
    <li class="breadcrumb-item active">{{ Str::limit($paper->title, 30) }}</li>
@endsection

@section('styles')
    <style>
        .paper-action-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .paper-sheet {
            width: 794px;
            max-width: 100%;
            min-height: 1123px;
            margin: 0 auto;
            padding: 40px 48px;
            background: #fff;
            box-shadow: 0 0 20px rgba(0,0,0,0.15);
            font-family: 'Times New Roman', 'Times', 'Liberation Serif', serif;
            font-size: 11pt;
            color: #111;
            line-height: 1.45;
            box-sizing: border-box;
        }
        .paper-hr {
            border: none;
            border-top: 1.5px solid #000;
            margin: 12px 0;
        }
        .paper-head-row {
            display: flex;
            align-items: center;
            gap: 16px;
        }
        .paper-logo {
            flex-shrink: 0;
        }
        .paper-logo img {
            max-height: 60px;
            max-width: 140px;
            object-fit: contain;
        }
        .paper-org { flex: 1; }
        .paper-org-name { font-size: 12pt; font-weight: 700; }
        .paper-org-sub { font-size: 10.5pt; color: #222; }
        .paper-subject-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }
        .paper-subject-name {
            font-size: 13pt;
            font-weight: 700;
            text-transform: uppercase;
        }
        .paper-subject-paper { font-size: 11pt; }
        .paper-subject-right { text-align: right; }
        .paper-code { font-size: 13pt; font-weight: 700; }
        .paper-code-line { font-size: 11pt; }
        .paper-materials { margin: 10px 0 0 20px; }
        .paper-materials-label { font-weight: 700; }
        .paper-materials-item { margin-left: 12px; }
        .paper-instructions-heading {
            font-size: 11pt;
            font-weight: 700;
            text-decoration: underline;
            text-align: center;
            margin-bottom: 8px;
        }
        .paper-instructions-body { white-space: pre-line; }
        .paper-footer-note {
            display: flex;
            justify-content: space-between;
            font-size: 9pt;
            color: #333;
        }
        .paper-section {
            margin-top: 24px;
        }
        .paper-section-header {
            font-size: 12pt;
            font-weight: 700;
            border-top: 1px solid #000;
            padding-top: 8px;
            margin-bottom: 2px;
        }
        .paper-section-intro { margin-bottom: 14px; }
        .paper-question { margin-bottom: 16px; }
        .paper-options { margin-left: 24px; }
        .paper-option { margin-bottom: 2px; }

        @media print {
            .main-sidebar, .main-header, .main-footer, .content-header,
            .paper-action-bar, .breadcrumb, .no-print {
                display: none !important;
            }
            .content-wrapper { margin-left: 0 !important; }
            .paper-sheet {
                box-shadow: none !important;
                width: 100% !important;
                min-height: auto !important;
                padding: 0 !important;
                margin: 0 !important;
            }
        }
        @page { size: A4; margin: 20mm; }
    </style>
@endsection

@section('content')
<div class="paper-action-bar no-print">
    <a href="{{ route('user.dashboard') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left mr-1"></i> Back to Dashboard
    </a>
    <div>
        @if($paper->type === 'auto')
            <a href="{{ url('/user/auto') }}?paper_id={{ $paper->id }}" class="btn btn-warning mr-1">
                <i class="fas fa-edit mr-1"></i> Edit
            </a>
        @else
            <a href="{{ url('/user/manual') }}?paper_id={{ $paper->id }}" class="btn btn-warning mr-1">
                <i class="fas fa-edit mr-1"></i> Edit
            </a>
        @endif
        <button type="button" class="btn btn-success" onclick="window.print()">
            <i class="fas fa-print mr-1"></i> Print
        </button>
    </div>
</div>

@if($paper->type === 'auto')
    @php
        $pd = $paper->paper_data ?? [];
        $mcqs = $pd['selectedMcqs'] ?? [];
    @endphp

    <div class="paper-sheet">
        {{-- Row 1: logo + org --}}
        <div class="paper-head-row">
            @if(!empty($pd['logoDataUrl']))
                <div class="paper-logo">
                    <img src="{{ $pd['logoDataUrl'] }}" alt="Logo">
                </div>
            @endif
            <div class="paper-org">
                <div class="paper-org-name">{{ $pd['schoolName'] ?? ($paper->school_name ?? '') }}</div>
                <div class="paper-org-sub">Cambridge Ordinary Level</div>
            </div>
        </div>

        <hr class="paper-hr">

        {{-- Row 3: subject / code / session --}}
        <div class="paper-subject-row">
            <div>
                <div class="paper-subject-name">{{ $paper->subject ?? '' }}</div>
                <div class="paper-subject-paper">Paper 1 Multiple Choice</div>
            </div>
            <div class="paper-subject-right">
                <div class="paper-code">{{ $pd['paperCode'] ?? '' }}</div>
                <div class="paper-code-line">{{ $pd['session'] ?? '' }}</div>
                <div class="paper-code-line">{{ $pd['duration'] ?? '' }}</div>
            </div>
        </div>

        {{-- Row 4: additional materials --}}
        @if(!empty($pd['additionalMaterials']))
        <div class="paper-materials">
            <div class="paper-materials-label">Additional Materials:</div>
            @foreach(preg_split('/\r\n|\r|\n/', $pd['additionalMaterials']) as $line)
                @if(trim($line) !== '')
                    <div class="paper-materials-item">{{ $line }}</div>
                @endif
            @endforeach
        </div>
        @endif

        <hr class="paper-hr">

        {{-- Row 6/7: instructions --}}
        @if(!empty($pd['instructions']))
        <div class="paper-instructions-heading">READ THESE INSTRUCTIONS FIRST</div>
        <div class="paper-instructions-body">{{ $pd['instructions'] }}</div>
        <hr class="paper-hr">
        @endif

        {{-- Row 9: footer note --}}
        <div class="paper-footer-note">
            <div>This document consists of {{ count($mcqs) }} printed pages and 2 blank pages.</div>
            <div>{{ $pd['paperCode'] ?? '' }}&nbsp;&nbsp;{{ $pd['session'] ?? '' }}</div>
        </div>

        {{-- Questions --}}
        <div class="paper-section">
            <div class="paper-section-header">Section A</div>
            <div class="paper-section-intro">Answer all {{ count($mcqs) }} questions.</div>

            @foreach($mcqs as $index => $mcq)
                <div class="paper-question">
                    <p><strong>{{ $index + 1 }}.</strong> {{ $mcq['stem_text'] ?? '' }}</p>
                    @if(!empty($mcq['stem_image']))
                        <img src="{{ $mcq['stem_image'] }}" style="max-width:100%; max-height:150px; display:block; margin:8px 0;">
                    @endif
                    <div class="paper-options">
                        @foreach(($mcq['options'] ?? []) as $opt)
                            <div class="paper-option">
                                <strong>{{ $opt['label'] }}</strong> {{ $opt['text'] ?? '' }}
                                @if(!empty($opt['image']))
                                    <img src="{{ $opt['image'] }}" style="max-height:60px; display:block; margin:4px 0 4px 24px;">
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>

@else
    {{-- Manual paper — metadata view --}}
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">
                        <span class="badge badge-manual mr-2">Manual</span>
                        {{ $paper->title }}
                    </h3>
                    <span class="badge badge-{{ $paper->status }}">{{ ucfirst($paper->status) }}</span>
                </div>
                <div class="card-body">
                    <table class="table table-borderless table-sm">
                        <tr><th width="160">School / Institution</th><td>{{ $paper->school_name ?? '—' }}</td></tr>
                        <tr><th>Grade</th><td>{{ $paper->grade ?? '—' }}</td></tr>
                        <tr><th>Subject</th><td>{{ $paper->subject ?? '—' }}</td></tr>
                        <tr><th>Exam Date</th><td>{{ $paper->exam_date ? $paper->exam_date->format('d F Y') : '—' }}</td></tr>
                        <tr><th>Questions</th><td>{{ $paper->question_count }}</td></tr>
                        <tr><th>Created</th><td>{{ $paper->created_at->format('d M Y, h:i A') }}</td></tr>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header"><h3 class="card-title">Actions</h3></div>
                <div class="card-body">
                    <a href="{{ route('user.papers.export', $paper->id) }}" class="btn btn-success btn-block mb-2">
                        <i class="fas fa-print mr-2"></i>Print / Export
                    </a>
                    <a href="{{ url('/user/manual') }}?paper_id={{ $paper->id }}" class="btn btn-warning btn-block mb-2">
                        <i class="fas fa-edit mr-2"></i>Edit in Designer
                    </a>
                    <button type="button" class="btn btn-danger btn-block delete-btn" data-id="{{ $paper->id }}" data-url="{{ route('user.papers.destroy', $paper->id) }}">
                        <i class="fas fa-trash mr-2"></i>Delete Paper
                    </button>
                    <form id="delete-form-{{ $paper->id }}" action="{{ route('user.papers.destroy', $paper->id) }}" method="POST" style="display: none;">
                        @csrf
                        @method('DELETE')
                    </form>
                </div>
            </div>
        </div>
    </div>
@endif
@endsection

@section('scripts')
<script src="{{ asset('adminlte/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
<script>
  $(function() {
    $('.delete-btn').on('click', function() {
        const id = $(this).data('id');
        const url = $(this).data('url');
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $(`#delete-form-${id}`).submit();
            }
        })
    });
  });
</script>
@endsection
