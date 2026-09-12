@extends('admin.layouts.master')

@section('title', "Preview: {$paper->title}")
@section('page_title', 'Paper Preview')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Users</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.user.papers.index', $user->id) }}">{{ $user->name }}'s Papers</a></li>
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
            .card-header, .paper-action-bar, .no-print {
                display: none !important;
            }
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
    <a href="{{ route('admin.user.papers.index', $user->id) }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left mr-1"></i> Back to Papers
    </a>
    <div>
        <a href="{{ route('admin.user.papers.edit', [$user->id, $paper->id]) }}" class="btn btn-warning mr-1">
            <i class="fas fa-edit mr-1"></i> Edit
        </a>
        <button type="button" class="btn btn-success" onclick="window.print()">
            <i class="fas fa-print mr-1"></i> Print
        </button>
    </div>
</div>

@if($paper->type === 'auto')
    @php
        $pd = $paper->paper_data ?? [];
        $mcqs = $pd['selectedMcqs'] ?? [];
        $displayTitle = $pd['paperTitle'] ?? $paper->title ?? 'Untitled';
        $displaySubject = $pd['subject'] ?? $paper->subject ?? '';
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
                <div class="paper-subject-name">{{ $displaySubject }}</div>
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
    {{-- MANUAL PAPER: Render from paper_data pages/blocks --}}
    @php
        $pd = $paper->paper_data ?? [];
        $pages = $pd['pages'] ?? [];
        $paperMeta = $pd['paperMeta'] ?? [];
    @endphp

    @foreach($pages as $pageIdx => $page)
        @if($pageIdx > 0)
            <div style="page-break-before: always; margin: 40px 0;"></div>
        @endif

        <div class="paper-sheet">
            {{-- Header for manual papers --}}
            @if($pageIdx === 0)
                @if(!empty($paperMeta['logo']))
                    <div class="paper-head-row">
                        <div class="paper-logo">
                            <img src="{{ $paperMeta['logo'] }}" alt="Logo">
                        </div>
                        <div class="paper-org">
                            <div class="paper-org-name">{{ $paperMeta['organization'] ?? '' }}</div>
                            <div class="paper-org-sub">{{ $paperMeta['subtitle'] ?? '' }}</div>
                        </div>
                    </div>
                    <hr class="paper-hr">
                @endif

                {{-- Subject/code row --}}
                <div class="paper-subject-row">
                    <div>
                        <div class="paper-subject-name">{{ $paperMeta['subject'] ?? '' }}</div>
                        <div class="paper-subject-paper">{{ $paperMeta['paperType'] ?? 'Paper 1' }}</div>
                    </div>
                    <div class="paper-subject-right">
                        <div class="paper-code">{{ $paperMeta['code'] ?? '' }}</div>
                        <div class="paper-code-line">{{ $paperMeta['date'] ?? '' }}</div>
                        <div class="paper-code-line">{{ $paperMeta['duration'] ?? '' }}</div>
                    </div>
                </div>

                @if(!empty($paperMeta['materialsLabel']))
                    <hr class="paper-hr">
                    <div class="paper-materials">
                        <div class="paper-materials-label">{{ $paperMeta['materialsLabel'] }}</div>
                        @if(!empty($paperMeta['materials']))
                            <div class="paper-materials-item">{{ $paperMeta['materials'] }}</div>
                        @endif
                    </div>
                @endif

                @if(!empty($paperMeta['instrHeading']))
                    <hr class="paper-hr">
                    <div class="paper-instructions-heading">{{ $paperMeta['instrHeading'] }}</div>
                    @if(!empty($paperMeta['instructions']))
                        <div class="paper-instructions-body">{!! $paperMeta['instructions'] !!}</div>
                    @endif
                @endif
            @endif

            {{-- Render blocks on this page --}}
            @foreach($page['blocks'] ?? [] as $block)
                @if($block['type'] === 'section')
                    <div class="paper-section">
                        <div class="paper-section-header">{{ $block['title'] ?? '' }}</div>
                        @if(!empty($block['subtitle']))
                            <div class="paper-section-intro">{{ $block['subtitle'] }}</div>
                        @endif
                    </div>

                @elseif($block['type'] === 'text')
                    <div style="margin-bottom: 12px;">
                        {!! $block['content'] ?? '' !!}
                    </div>

                @elseif($block['type'] === 'divider')
                    <hr class="paper-hr" style="border-style: {{ $block['style'] ?? 'solid' }};">

                @elseif($block['type'] === 'image')
                    <div style="text-align: center; margin: 12px 0;">
                        <img src="{{ $block['src'] ?? '' }}" style="width: {{ $block['width'] ?? 60 }}%; max-width: 100%; object-fit: contain;">
                        @if(!empty($block['caption']))
                            <p style="font-size: 9pt; color: #666; margin-top: 4px;">{{ $block['caption'] }}</p>
                        @endif
                    </div>

                @elseif($block['type'] === 'table')
                    <table style="width: 100%; border-collapse: collapse; margin: 12px 0;">
                        @if(!empty($block['headers']))
                            <thead>
                                <tr>
                                    @foreach($block['headers'] as $header)
                                        <th style="border: 1px solid #000; padding: 6px; background: #f0f0f0; font-weight: bold; text-align: center;">{{ $header }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                        @endif
                        <tbody>
                            @foreach($block['data'] ?? [] as $row)
                                <tr>
                                    @foreach($row as $cell)
                                        <td style="border: 1px solid #000; padding: 6px;">{{ $cell }}</td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                @elseif($block['type'] === 'mcq')
                    <div style="margin-bottom: 16px; page-break-inside: avoid;">
                        <p style="margin-bottom: 6px;">
                            <strong>{{ $block['qNum'] ?? $loop->index + 1 }}.</strong>
                            {!! $block['stem'] ?? '' !!}
                        </p>
                        @if(!empty($block['imageData']) && $block['imagePos'] !== 'below')
                            <img src="{{ $block['imageData'] }}" style="max-width: 150px; max-height: 100px; display: block; margin: 4px 0;">
                        @endif
                        @if(!empty($block['imageData']) && $block['imagePos'] === 'below')
                            <img src="{{ $block['imageData'] }}" style="max-width: 150px; max-height: 100px; display: block; margin: 4px 0;">
                        @endif
                        @if($block['showMarks'] !== false && !empty($block['marks']))
                            <p style="font-size: 9pt; color: #666; margin-bottom: 4px;"> [{{ $block['marks'] }}]</p>
                        @endif
                        <div style="margin-left: 24px; margin-top: 4px;">
                            @foreach($block['options'] ?? [] as $optIdx => $opt)
                                @php
                                    $optLabel = ['A', 'B', 'C', 'D', 'E', 'F'][$optIdx] ?? '';
                                @endphp
                                <p style="margin-bottom: 4px;">
                                    <strong>{{ $optLabel }}.</strong>
                                    @if(is_array($opt))
                                        {{ $opt['text'] ?? $opt }}
                                    @else
                                        {{ $opt }}
                                    @endif
                                </p>
                                @if(is_array($opt) && !empty($opt['image']))
                                    <img src="{{ $opt['image'] }}" style="max-height: 60px; display: block; margin: 4px 0 4px 24px;">
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    @endforeach

@endif

@endsection
