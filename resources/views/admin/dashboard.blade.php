@extends('admin.layouts.master')

@section('title', 'Dashboard')
@section('page_title', 'Dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('styles')
  <!-- DataTables -->
  <link rel="stylesheet" href="{{ asset('adminlte/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
  <link rel="stylesheet" href="{{ asset('adminlte/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
@endsection

@section('content')
    <!-- Info boxes -->
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
        <div class="info-box mb-3">
          <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-question-circle"></i></span>
          <div class="info-box-content">
            <span class="info-box-text">Total Questions</span>
            <span class="info-box-number">{{ $totalQuestions ?? 0 }}</span>
          </div>
        </div>
      </div>
      <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box mb-3">
          <span class="info-box-icon bg-success elevation-1"><i class="fas fa-users"></i></span>
          <div class="info-box-content">
            <span class="info-box-text">Total Users</span>
            <span class="info-box-number">{{ $totalUsers ?? 0 }}</span>
          </div>
        </div>
      </div>
      <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box mb-3">
          <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-file-export"></i></span>
          <div class="info-box-content">
            <span class="info-box-text">Pending Exports</span>
            <span class="info-box-number">{{ $pendingExports ?? 0 }}</span>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-md-8">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Recent Exam Papers</h3>
            <div class="card-tools">
                <a href="{{ route('admin.papers.index') }}" class="btn btn-sm btn-primary">View All</a>
            </div>
          </div>
          <div class="card-body">
            <table id="recent-papers" class="table table-bordered table-striped">
              <thead>
              <tr>
                <th>Title</th>
                <th>Subject</th>
                <th>Year</th>
                <th>Status</th>
                <th>Date</th>
              </tr>
              </thead>
              <tbody>
              @isset($recentPapers)
                  @foreach($recentPapers as $paper)
                  <tr>
                    <td>{{ $paper->title }}</td>
                    <td>{{ $paper->subject }}</td>
                    <td>{{ $paper->year }}</td>
                    <td>
                        @if($paper->status == 'published')
                            <span class="badge badge-success">Published</span>
                        @else
                            <span class="badge badge-warning">Draft</span>
                        @endif
                    </td>
                    <td>{{ $paper->created_at->format('Y-m-d') }}</td>
                  </tr>
                  @endforeach
              @else
                  <tr>
                    <td colspan="5" class="text-center">No recent papers found.</td>
                  </tr>
              @endisset
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <div class="col-md-4">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Recent Questions</h3>
            <div class="card-tools">
                <a href="{{ route('admin.questions.index') }}" class="btn btn-sm btn-primary">View All</a>
            </div>
          </div>
          <div class="card-body p-0">
            <ul class="products-list product-list-in-card pl-2 pr-2">
              @isset($recentQuestions)
                  @foreach($recentQuestions as $question)
                  <li class="item">
                    <div class="product-info ml-0">
                      <a href="{{ route('admin.questions.show', $question->id) }}" class="product-title">
                        {{ Str::limit(strip_tags($question->data['stem_text'] ?? '—'), 50) }}
                        <span class="badge badge-info float-right">{{ $question->marks }} Marks</span>
                      </a>
                      <span class="product-description">
                        {{ $question->subject }} - {{ $question->topic }}
                      </span>
                    </div>
                  </li>
                  @endforeach
              @else
                  <li class="item">
                    <div class="text-center p-3">No recent questions found.</div>
                  </li>
              @endisset
            </ul>
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

<script>
  $(function () {
    $("#recent-papers").DataTable({
      "responsive": true,
      "lengthChange": false,
      "autoWidth": false,
      "searching": false,
      "paging": false,
      "info": false,
      "order": [[4, "desc"]]
    });
  });
</script>
@endsection
