@extends('layouts.app')

@section('title', 'Add Client — Super Admin')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.index') }}">Super Admin</a></li>
<li class="breadcrumb-item"><a href="{{ route('admin.clients.index') }}">Clients</a></li>
<li class="breadcrumb-item active">Add Client</li>
@endsection

@section('content')

<div class="row justify-content-center">
    <div class="col-md-7">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-building-add me-2"></i>Add New Client</span>
                <a href="{{ route('admin.clients.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left me-1"></i>Back
                </a>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.clients.store') }}" method="POST">
                    @csrf
                    @include('admin.clients._form')
                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i>Create Client
                        </button>
                        <a href="{{ route('admin.clients.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
