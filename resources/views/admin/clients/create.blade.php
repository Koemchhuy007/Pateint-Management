@extends('layouts.app')

@section('title', __('admin.create_client'))

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.index') }}">{{ __('admin.dashboard') }}</a></li>
<li class="breadcrumb-item"><a href="{{ route('admin.clients.index') }}">{{ __('admin.clients') }}</a></li>
<li class="breadcrumb-item active">{{ __('admin.add_client') }}</li>
@endsection

@section('content')

<div class="row justify-content-center">
    <div class="col-md-7">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-building-add me-2"></i>{{ __('admin.create_client') }}</span>
                <a href="{{ route('admin.clients.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left me-1"></i>{{ __('common.back') }}
                </a>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.clients.store') }}" method="POST">
                    @csrf
                    @include('admin.clients._form')
                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i>{{ __('admin.create_client') }}
                        </button>
                        <a href="{{ route('admin.clients.index') }}" class="btn btn-outline-secondary">{{ __('common.cancel') }}</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
