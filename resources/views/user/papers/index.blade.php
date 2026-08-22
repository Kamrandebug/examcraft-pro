@extends('user.layouts.app')

@section('title', 'My Papers')
@section('page_title', 'My Papers')

@section('breadcrumb')
    <li class="breadcrumb-item active">My Papers</li>
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
                <h3 class="card-title"><i class="fas fa-file-alt mr-2"></i>All My Papers</h3>
                <div class="card-tools">
                    <a href="{{ route('user.manual') }}" class="btn btn-info btn-sm">
                        <i class="fas fa-pencil-alt mr-1"></i>Manual
                    </a>
                    <a href="{{ route('user.auto') }}" class="btn btn-sm" style="background:#6f42c1;color:#fff;">
                        <i class="fas fa-magic mr-1"></i>Auto
                    </a>
                </div>
            </div>
            <div class="card-body">
                <table id="papers-table" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Title</th>
                            <th>Type</th>
                            <th>Grade</th>
                            <th>Subject</th>
                            <th>School</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($papers as $paper)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <a href="{{ route('user.papers.show', $paper->id) }}" class="font-weight-bold text-dark">
                                    {{ $paper->title }}
                                </a>
                            </td>
                            <td>
                                <span class="badge badge-{{ $paper->type === 'auto' ? 'auto' : 'manual' }}">
                                    <i class="fas fa-{{ $paper->type === 'auto' ? 'magic' : 'pencil-alt' }} mr-1"></i>
                                    {{ ucfirst($paper->type) }}
                                </span>
                            </td>
                            <td>{{ $paper->grade ?? '—' }}</td>
                            <td>{{ $paper->subject ?? '—' }}</td>
                            <td>{{ $paper->school_name ?? '—' }}</td>
                            <td>
                                <span class="badge badge-{{ $paper->status }}">{{ ucfirst($paper->status) }}</span>
                            </td>
                            <td>{{ $paper->created_at->format('d M Y') }}</td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('user.papers.show', $paper->id) }}" class="btn btn-info btn-sm" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if($paper->type === 'auto')
                                        <a href="{{ url('/user/auto') }}?paper_id={{ $paper->id }}" class="btn btn-warning btn-sm" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    @else
                                        <a href="{{ url('/user/manual') }}?paper_id={{ $paper->id }}" class="btn btn-warning btn-sm" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    @endif
                                    <a href="{{ route('user.papers.export', $paper->id) }}" class="btn btn-success btn-sm" title="Print / Export">
                                        <i class="fas fa-print"></i>
                                    </a>
                                    <button type="button" class="btn btn-danger btn-sm delete-btn" data-id="{{ $paper->id }}" data-url="{{ route('user.papers.destroy', $paper->id) }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                                <form id="delete-form-{{ $paper->id }}" action="{{ route('user.papers.destroy', $paper->id) }}" method="POST" style="display: none;">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
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
<script src="{{ asset('adminlte/plugins/jszip/jszip.min.js') }}"></script>
<script src="{{ asset('adminlte/plugins/pdfmake/pdfmake.min.js') }}"></script>
<script src="{{ asset('adminlte/plugins/pdfmake/vfs_fonts.js') }}"></script>
<script src="{{ asset('adminlte/plugins/datatables-buttons/js/buttons.html5.min.js') }}"></script>
<script src="{{ asset('adminlte/plugins/datatables-buttons/js/buttons.print.min.js') }}"></script>
<script src="{{ asset('adminlte/plugins/datatables-buttons/js/buttons.colVis.min.js') }}"></script>
<script src="{{ asset('adminlte/plugins/sweetalert2/sweetalert2.min.js') }}"></script>

<script>
  $(function () {
    $("#papers-table").DataTable({
      "responsive": true,
      "lengthChange": true,
      "autoWidth": false,
      "order": [[7, "desc"]],
      "columnDefs": [
        { "orderable": false, "targets": [8] }
      ],
      "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
    }).buttons().container().appendTo('#papers-table_wrapper .col-md-6:eq(0)');

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
