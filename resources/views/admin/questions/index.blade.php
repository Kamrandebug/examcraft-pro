@extends('admin.layouts.master')

@section('title', 'All Questions')
@section('page_title', 'Question Bank')

@section('breadcrumb')
    <li class="breadcrumb-item active">Question Bank</li>
@endsection

@section('styles')
  <!-- DataTables -->
  <link rel="stylesheet" href="{{ asset('adminlte/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
  <link rel="stylesheet" href="{{ asset('adminlte/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
  <link rel="stylesheet" href="{{ asset('adminlte/plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
  <!-- SweetAlert2 -->
  <link rel="stylesheet" href="{{ asset('adminlte/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css') }}">
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">All Questions</h3>
                <div class="card-tools">
                    <a href="{{ route('admin.questions.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Add New Question
                    </a>
                </div>
            </div>
            <div class="card-body">
                <table id="questions-table" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Question</th>
                            <th>Subject</th>
                            <th>Topic</th>
                            <th>Marks</th>
                            <th>Type</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @isset($questions)
                            @foreach($questions as $question)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ Str::limit(strip_tags($question->stem), 100) }}</td>
                                <td>{{ $question->subject }}</td>
                                <td>{{ $question->topic }}</td>
                                <td>{{ $question->marks }}</td>
                                <td>
                                    <span class="badge badge-{{ $question->option_type == 'MCQ' ? 'primary' : ($question->option_type == 'Text' ? 'info' : 'secondary') }}">
                                        {{ $question->option_type }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('admin.questions.show', $question->id) }}" class="btn btn-info btn-sm">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.questions.edit', $question->id) }}" class="btn btn-warning btn-sm">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-danger btn-sm delete-btn" data-id="{{ $question->id }}" data-url="{{ route('admin.questions.destroy', $question->id) }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                    <form id="delete-form-{{ $question->id }}" action="{{ route('admin.questions.destroy', $question->id) }}" method="POST" style="display: none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        @endisset
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- DataTables  & Plugins -->
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
<!-- SweetAlert2 -->
<script src="{{ asset('adminlte/plugins/sweetalert2/sweetalert2.min.js') }}"></script>

<script>
  $(function () {
    $("#questions-table").DataTable({
      "responsive": true, 
      "lengthChange": true, 
      "autoWidth": false,
      "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
    }).buttons().container().appendTo('#questions-table_wrapper .col-md-6:eq(0)');

    // SweetAlert2 Delete Confirmation
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
