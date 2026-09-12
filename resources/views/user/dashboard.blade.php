@extends('user.layouts.app')

@section('title', 'My Dashboard')
@section('page_title', 'My Dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('content')
    {{-- Stats Row --}}
    <div class="row">
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-info elevation-1"><i class="fas fa-file-alt"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Papers</span>
                    <span class="info-box-number">{{ $totalPapers ?? 0 }}</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-purple elevation-1"><i class="fas fa-magic"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Auto Generated</span>
                    <span class="info-box-number">{{ $autoPapers ?? 0 }}</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-cyan elevation-1"><i class="fas fa-pencil-alt"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Manual Papers</span>
                    <span class="info-box-number">{{ $manualPapers ?? 0 }}</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-success elevation-1"><i class="fas fa-check-circle"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Published</span>
                    <span class="info-box-number">{{ $publishedPapers ?? 0 }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body text-center">
                    <i class="fas fa-pencil-alt fa-3x mb-3" style="color:#17a2b8;"></i>
                    <h5 class="card-title">Create Manual Paper</h5>
                    <p class="card-text text-muted small">Full design control. Add MCQs, sections, images, tables with drag-and-drop.</p>
                    <a href="{{ route('user.manual') }}" class="btn btn-info px-4">
                        <i class="fas fa-plus mr-1"></i> Start Designing
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-body text-center">
                    <i class="fas fa-magic fa-3x mb-3" style="color:#6f42c1;"></i>
                    <h5 class="card-title">Auto Paper Generator</h5>
                    <p class="card-text text-muted small">Select grade and subject, let the system build a formatted paper automatically.</p>
                    <a href="{{ route('user.auto') }}" class="btn px-4" style="background:#6f42c1;color:#fff;">
                        <i class="fas fa-magic mr-1"></i> Generate Paper
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Recent Papers --}}
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-history mr-2"></i>Recent Papers</h3>
            <div class="card-tools">
                <a href="{{ route('user.papers.index') }}" class="btn btn-sm btn-outline-secondary">
                    View All <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
        </div>
        <div class="card-body table-responsive p-0">
            <table class="table table-hover text-nowrap mb-0">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Type</th>
                        <th>Subject</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentPapers as $paper)
                    <tr>
                        <td>
                            <a href="{{ route('user.papers.show', $paper->id) }}" class="font-weight-bold text-dark">
                                {{ $paper->title }}
                            </a>
                        </td>
                        <td>
                            <span class="badge badge-{{ $paper->type === 'auto' ? 'auto' : 'manual' }}">
                                {{ ucfirst($paper->type) }}
                            </span>
                        </td>
                        <td>{{ $paper->subject ?? '—' }}</td>
                        <td>
                            <span class="badge badge-{{ $paper->status }}">{{ ucfirst($paper->status) }}</span>
                        </td>
                        <td>{{ $paper->created_at->diffForHumans() }}</td>
                        <td class="text-center">
                            @include('partials.action-buttons', [
                                'viewRoute' => route('user.papers.show', $paper->id),
                                'editRoute' => $paper->type === 'auto' ? url('/user/auto').'?paper_id='.$paper->id : url('/user/manual').'?paper_id='.$paper->id,
                                'printRoute' => route('user.papers.export', $paper->id)
                            ])
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">
                            <i class="fas fa-file-alt fa-2x mb-2 d-block"></i>
                            No papers yet. Create your first paper above!
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
