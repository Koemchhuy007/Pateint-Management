@extends('layouts.app')
@section('title', $serviceGroup->name . ' — Services')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('settings.service-groups.index') }}">Settings / Service Groups</a></li>
<li class="breadcrumb-item active">{{ $serviceGroup->name }}</li>
@endsection

@section('content')
<div class="row g-4 align-items-start">

    @include('settings._sidebar')

    <div class="col-lg-10 col-md-9">

        <div class="d-flex align-items-center gap-3 mb-4">
            <a href="{{ route('settings.service-groups.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div>
                <h5 class="fw-bold mb-0" style="color:#1e293b;">
                    <i class="bi bi-list-check me-2 text-success"></i>{{ $serviceGroup->name }}
                    <span class="text-muted fw-normal" style="font-size:.9rem;">— Services</span>
                </h5>
                @if($serviceGroup->description)
                    <div class="text-muted mt-1" style="font-size:.82rem;">{{ $serviceGroup->description }}</div>
                @endif
            </div>
        </div>

        <div class="row g-4 align-items-start">

            {{-- Add form --}}
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <i class="bi bi-plus-circle me-2 text-success"></i>Add Service
                    </div>
                    <div class="card-body">
                        <form action="{{ route('settings.service-groups.services.store', $serviceGroup) }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label fw-semibold" style="font-size:.83rem;">
                                    Service Name <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="name"
                                       class="form-control @error('name') is-invalid @enderror"
                                       value="{{ old('name') }}"
                                       placeholder="e.g. Blood Test, X-Ray…" required>
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold" style="font-size:.83rem;">
                                    Price ($) <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted">$</span>
                                    <input type="number" name="price"
                                           class="form-control border-start-0 @error('price') is-invalid @enderror"
                                           value="{{ old('price', '0') }}"
                                           min="0" step="0.01" required>
                                </div>
                                @error('price')<div class="text-danger mt-1" style="font-size:.82rem;">{{ $message }}</div>@enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold" style="font-size:.83rem;">Description</label>
                                <input type="text" name="description" class="form-control"
                                       value="{{ old('description') }}"
                                       placeholder="Optional notes">
                            </div>
                            <button type="submit" class="btn btn-primary btn-sm w-100">
                                <i class="bi bi-plus-lg me-1"></i>Add Service
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Services list --}}
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <i class="bi bi-list-ul me-2"></i>Services in "{{ $serviceGroup->name }}"
                        <span class="badge bg-secondary ms-1" style="font-size:.7rem;">{{ $services->count() }}</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" style="font-size:.88rem;">
                            <thead style="background:#f8fafc;">
                                <tr>
                                    <th class="ps-4">Service Name</th>
                                    <th>Description</th>
                                    <th style="width:90px;" class="text-end">Price</th>
                                    <th style="width:80px;" class="text-center">Status</th>
                                    <th style="width:110px;" class="text-end pe-4">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($services as $service)
                                <tr>
                                    <td class="ps-4 fw-semibold" style="color:#1e293b;">{{ $service->name }}</td>
                                    <td class="text-muted" style="font-size:.82rem;">{{ $service->description ?: '—' }}</td>
                                    <td class="text-end fw-semibold" style="color:#15803d;">
                                        ${{ number_format($service->price, 2) }}
                                    </td>
                                    <td class="text-center">
                                        @if($service->is_active)
                                            <span class="badge" style="background:#dcfce7;color:#15803d;font-size:.72rem;">Active</span>
                                        @else
                                            <span class="badge bg-secondary" style="font-size:.72rem;">Inactive</span>
                                        @endif
                                    </td>
                                    <td class="text-end pe-4">
                                        <button type="button" class="btn btn-sm btn-outline-primary"
                                                data-bs-toggle="modal"
                                                data-bs-target="#editSvc{{ $service->id }}">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <form action="{{ route('settings.service-groups.services.destroy', [$serviceGroup, $service]) }}"
                                              method="POST" class="d-inline"
                                              onsubmit="return confirm('Delete service \'{{ addslashes($service->name) }}\'?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted">
                                        <i class="bi bi-list-check d-block mb-2" style="font-size:2rem;opacity:.3;"></i>
                                        No services yet. Add one on the left.
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
@foreach($services as $service)
<div class="modal fade" id="editSvc{{ $service->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header py-3">
                <h6 class="modal-title fw-bold">Edit Service</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('settings.service-groups.services.update', [$serviceGroup, $service]) }}" method="POST">
                @csrf @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:.83rem;">Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{ $service->name }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:.83rem;">Price ($) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted">$</span>
                            <input type="number" name="price" class="form-control border-start-0"
                                   value="{{ $service->price }}" min="0" step="0.01" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:.83rem;">Description</label>
                        <input type="text" name="description" class="form-control" value="{{ $service->description }}">
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1"
                               id="svcActive{{ $service->id }}" {{ $service->is_active ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold" for="svcActive{{ $service->id }}" style="font-size:.83rem;">
                            Active
                        </label>
                    </div>
                </div>
                <div class="modal-footer py-2">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-primary">
                        <i class="bi bi-check-lg me-1"></i>Save
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

@endsection
