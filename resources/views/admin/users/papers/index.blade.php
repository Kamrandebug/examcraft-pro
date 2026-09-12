@extends('admin.layouts.master')

@section('title', "Papers for {$user->name}")
@section('page_title', "User: {$user->name}")

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Users</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.users.edit', $user->id) }}">{{ $user->name }}</a></li>
    <li class="breadcrumb-item active">Papers</li>
@endsection

@section('styles')
  <link rel="stylesheet" href="{{ asset('adminlte/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
  <link rel="stylesheet" href="{{ asset('adminlte/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
  <link rel="stylesheet" href="{{ asset('adminlte/plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
  <link rel="stylesheet" href="{{ asset('adminlte/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css') }}">
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-file-alt mr-2"></i>Papers for {{ $user->name }}
                </h3>
                <div class="card-tools">
                    <div class="btn-group">
                        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left mr-1"></i> Back to Users
                        </a>
                        <a href="{{ route('admin.user.papers.create', $user->id) }}?type=manual" class="btn btn-primary btn-sm">
                            <i class="fas fa-pencil-alt mr-1"></i> Manual
                        </a>
                        <a href="{{ route('admin.user.papers.create', $user->id) }}?type=auto" class="btn btn-sm" style="background:#6f42c1;color:#fff;">
                            <i class="fas fa-magic mr-1"></i> Auto
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                @if($papers->count() > 0)
                <table id="papers-table" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Title</th>
                            <th>Type</th>
                            <th>Questions</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($papers as $paper)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $paper->title }}</td>
                            <td>
                                <span class="badge badge-{{ $paper->type === 'auto' ? 'info' : 'warning' }}">
                                    <i class="fas fa-{{ $paper->type === 'auto' ? 'magic' : 'pencil-alt' }} mr-1"></i>
                                    {{ ucfirst($paper->type) }}
                                </span>
                            </td>
                            <td><span class="badge badge-secondary">{{ $paper->question_count }}</span></td>
                            <td>
                                <button type="button" class="btn btn-sm status-toggle-btn"
                                        data-id="{{ $paper->id }}"
                                        data-status="{{ $paper->status }}"
                                        data-user-id="{{ $user->id }}"
                                        title="Click to toggle status">
                                    <span class="badge badge-{{ $paper->status === 'published' ? 'success' : 'warning' }}">
                                        {{ ucfirst($paper->status) }}
                                    </span>
                                </button>
                            </td>
                            <td>{{ $paper->created_at->format('Y-m-d H:i') }}</td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('admin.user.papers.show', [$user->id, $paper->id]) }}" class="btn btn-info btn-sm" title="Preview">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.user.papers.edit', [$user->id, $paper->id]) }}" class="btn btn-warning btn-sm" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="{{ route('admin.user.papers.export', [$user->id, $paper->id]) }}" class="btn btn-success btn-sm" title="Export">
                                        <i class="fas fa-download"></i>
                                    </a>
                                    <button type="button" class="btn btn-danger btn-sm delete-btn" data-id="{{ $paper->id }}" data-user-id="{{ $user->id }}" data-url="{{ route('admin.user.papers.destroy', [$user->id, $paper->id]) }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                                <form id="delete-form-{{ $paper->id }}" action="{{ route('admin.user.papers.destroy', [$user->id, $paper->id]) }}" method="POST" style="display: none;">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                <div class="alert alert-info">
                    <i class="fas fa-info-circle mr-2"></i> No papers yet.
                    <a href="{{ route('admin.user.papers.create', $user->id) }}?type=manual">Create a manual paper</a> or
                    <a href="{{ route('admin.user.papers.create', $user->id) }}?type=auto">create an auto paper</a>.
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('adminlte/plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('adminlte/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('adminlte/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('adminlte/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
<script src="{{ asset('adminlte/plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('adminlte/plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>
<script src="{{ asset('adminlte/plugins/sweetalert2/sweetalert2.min.js') }}"></script>

<script>
$(function () {
    $("#papers-table").DataTable({
        "responsive": true,
        "lengthChange": true,
        "autoWidth": false,
        "order": [[5, "desc"]]
    });

    // Delete paper
    $('.delete-btn').on('click', function() {
        const id = $(this).data('id');
        const userId = $(this).data('user-id');
        const url = $(this).data('url');

        Swal.fire({
            title: 'Are you sure?',
            text: "This paper will be permanently deleted!",
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

    // Toggle paper status
    $('.status-toggle-btn').on('click', function(e) {
        e.preventDefault();
        const btn = $(this);
        const paperId = btn.data('id');
        const userId = btn.data('user-id');
        const currentStatus = btn.data('status');

        $.ajax({
            url: `/admin/users/${userId}/papers/${paperId}/status`,
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Accept': 'application/json'
            },
            success: function(response) {
                // Reload the page to show updated status
                location.reload();
            },
            error: function(err) {
                Swal.fire('Error', 'Failed to update paper status', 'error');
                console.error(err);
            }
        });
    });
});
</script>
@endsection
