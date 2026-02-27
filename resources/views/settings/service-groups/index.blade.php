@extends('layouts.app')
@section('title', __('settings.service_groups'))

@section('breadcrumb')
<li class="breadcrumb-item active">{{ __('nav.setting') }}</li>
<li class="breadcrumb-item active">{{ __('settings.service_groups') }}</li>
@endsection

@section('content')
<div class="row g-4 align-items-start">

    @include('settings._sidebar')

    <div class="col-lg-10 col-md-9">

        <h5 class="fw-bold mb-4" style="color:#1e293b;">
            <i class="bi bi-grid-fill me-2 text-primary"></i>{{ __('settings.service_groups') }}
        </h5>

        <div class="row g-4 align-items-start">

            {{-- Add form --}}
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <i class="bi bi-plus-circle me-2 text-success"></i>{{ __('settings.service_add') }}
                    </div>
                    <div class="card-body">
                        <form action="{{ route('settings.service-groups.store') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label fw-semibold" style="font-size:.83rem;">
                                    {{ __('common.name') }} <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="name"
                                       class="form-control @error('name') is-invalid @enderror"
                                       value="{{ old('name') }}"
                                       placeholder="{{ __('placeholder.service_group_name') }}" required>
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold" style="font-size:.83rem;">{{ __('common.description') }}</label>
                                <input type="text" name="description" class="form-control"
                                       value="{{ old('description') }}"
                                       placeholder="{{ __('placeholder.optional_description') }}">
                            </div>
                            <button type="submit" class="btn btn-primary btn-sm w-100">
                                <i class="bi bi-plus-lg me-1"></i>{{ __('settings.service_add') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- List --}}
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <i class="bi bi-list-ul me-2"></i>{{ __('settings.all_service_groups') }}
                        <span class="badge bg-secondary ms-1" style="font-size:.7rem;">{{ $serviceGroups->count() }}</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" style="font-size:.88rem;">
                            <thead style="background:#f8fafc;">
                                <tr>
                                    <th class="ps-4">{{ __('common.name') }}</th>
                                    <th>{{ __('common.description') }}</th>
                                    <th style="width:90px;" class="text-center">{{ __('settings.manage_services') }}</th>
                                    <th style="width:150px;" class="text-end pe-4">{{ __('common.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($serviceGroups as $group)
                                <tr>
                                    <td class="ps-4 fw-semibold" style="color:#1e293b;">{{ $group->name }}</td>
                                    <td class="text-muted">{{ $group->description ?: '—' }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('settings.service-groups.services.index', $group) }}"
                                           class="badge text-decoration-none"
                                           style="background:#eff6ff;color:#2563eb;font-size:.78rem;"
                                           title="{{ __('settings.manage_services') }}">
                                            {{ $group->services_count }} {{ __('settings.services') }}
                                        </a>
                                    </td>
                                    <td class="text-end pe-4">
                                        <a href="{{ route('settings.service-groups.services.index', $group) }}"
                                           class="btn btn-sm btn-outline-success" title="Manage Services">
                                            <i class="bi bi-list-check"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-primary"
                                                data-bs-toggle="modal"
                                                data-bs-target="#editSG{{ $group->id }}">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <form action="{{ route('settings.service-groups.destroy', $group) }}" method="POST"
                                              class="d-inline"
                                              onsubmit="return confirm('Delete group \'{{ addslashes($group->name) }}\'?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger"
                                                    @if($group->services_count > 0) disabled title="Has services" @endif>
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-5 text-muted">
                                        <i class="bi bi-grid d-block mb-2" style="font-size:2rem;opacity:.3;"></i>
                                        {{ __('settings.no_service_groups') }}
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

{{-- Edit modals --}}
@foreach($serviceGroups as $group)
<div class="modal fade" id="editSG{{ $group->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header py-3">
                <h6 class="modal-title fw-bold">{{ __('settings.service_edit') }}</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('settings.service-groups.update', $group) }}" method="POST">
                @csrf @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:.83rem;">{{ __('common.name') }} <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{ $group->name }}" required>
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-semibold" style="font-size:.83rem;">{{ __('common.description') }}</label>
                        <input type="text" name="description" class="form-control" value="{{ $group->description }}">
                    </div>
                </div>
                <div class="modal-footer py-2">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">{{ __('common.cancel') }}</button>
                    <button type="submit" class="btn btn-sm btn-primary">
                        <i class="bi bi-check-lg me-1"></i>{{ __('common.save') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

@endsection
