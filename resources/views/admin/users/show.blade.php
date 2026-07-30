@extends('admin.layouts.master')

@section('title', 'User Profile')
@section('page_title', 'User Profile: ' . $user->name)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Users</a></li>
    <li class="breadcrumb-item active">Profile</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-4">
        <!-- Profile Image -->
        <div class="card card-primary card-outline">
            <div class="card-body box-profile">
                <div class="text-center">
                    <div class="profile-user-img img-fluid img-circle" style="background:#eee;width:100px;height:100px;margin:0 auto;display:flex;align-items:center;justify-content:center;font-size:40px;font-weight:bold;color:#999;">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                </div>

                <h3 class="profile-username text-center">{{ $user->name }}</h3>

                <p class="text-muted text-center">{{ ucfirst($user->role?->name ?? 'User') }}</p>

                <ul class="list-group list-group-unbordered mb-3">
                    <li class="list-group-item">
                        <b>Email</b> <a class="float-right">{{ $user->email }}</a>
                    </li>
                    <li class="list-group-item">
                        <b>Joined</b> <a class="float-right">{{ $user->created_at->format('M d, Y') }}</a>
                    </li>
                </ul>

                <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-warning btn-block"><b>Edit Profile</b></a>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card">
            <div class="card-header p-2">
                <ul class="nav nav-pills">
                    <li class="nav-item"><a class="nav-link active" href="#activity" data-toggle="tab">Activity Summary</a></li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content">
                    <div class="active tab-pane" id="activity">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="info-box bg-light">
                                    <div class="info-box-content">
                                        <span class="info-box-text text-center text-muted">Exam Papers</span>
                                        <span class="info-box-number text-center text-muted mb-0">{{ $user->examPapers->count() }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="info-box bg-light">
                                    <div class="info-box-content">
                                        <span class="info-box-text text-center text-muted">Questions Contributed</span>
                                        <span class="info-box-number text-center text-muted mb-0">{{ $user->questionBank->count() }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
