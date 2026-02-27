@extends('layouts.app')

@section('title', 'Drug Types')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('drugstore.index') }}">Drugstore</a></li>
<li class="breadcrumb-item active">Drug Types</li>
@endsection

@section('content')

<div class="d-flex align-items-center justify-content-between mb-4">
    <h5 class="fw-bold mb-0" style="color:#1e293b;">
        <i class="bi bi-tags me-2 text-primary"></i>{{ __('drug.types') }}
    </h5>
    <a href="{{ route('drugstore.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i>{{ __('common.back') }}
    </a>
</div>

<div class="row g-4 align-items-start">

    {{-- ── Add Type ── --}}
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-plus-circle me-2 text-success"></i>{{ __('drug.add_new_type') }}
            </div>
            <div class="card-body">
                <form action="{{ route('drug-types.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:.83rem;">
                            {{ __('drug.type_name') }} <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="name"
                               class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name') }}"
                               placeholder="{{ __('placeholder.type_name') }}"
                               required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:.83rem;">Description</label>
                        <input type="text" name="description"
                               class="form-control"
                               value="{{ old('description') }}"
                               placeholder="Optional description">
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm w-100">
                        <i class="bi bi-plus-lg me-1"></i>{{ __('drug.save_type') }}
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- ── Types list ── --}}
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-list-ul me-2"></i>All Types
                <span class="badge bg-secondary ms-2" style="font-size:.7rem;">{{ $types->count() }}</span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size:.88rem;">
                    <thead style="background:#f8fafc;">
                        <tr>
                            <th class="ps-4">Type Name</th>
                            <th>Description</th>
                            <th style="width:90px;" class="text-center">Drugs</th>
                            <th style="width:110px;" class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($types as $type)
                        <tr>
                            <td class="ps-4 fw-semibold" style="color:#1e293b;">{{ $type->name }}</td>
                            <td class="text-muted">{{ $type->description ?: '—' }}</td>
                            <td class="text-center">
                                <a href="{{ route('drugstore.index', ['type_id' => $type->id]) }}"
                                   class="badge text-decoration-none"
                                   style="background:#eff6ff;color:#2563eb;font-size:.78rem;">
                                    {{ $type->drugs_count }}
                                </a>
                            </td>
                            <td class="text-end pe-4">
                                {{-- Edit trigger --}}
                                <button type="button" class="btn btn-sm btn-outline-primary"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editModal{{ $type->id }}">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                {{-- Delete --}}
                                <form action="{{ route('drug-types.destroy', $type) }}" method="POST"
                                      class="d-inline"
                                      onsubmit="return confirm('Delete type \'{{ addslashes($type->name) }}\'?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger"
                                            @if($type->drugs_count > 0) disabled title="Has drugs assigned" @endif>
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted">
                                <i class="bi bi-tags d-block mb-2" style="font-size:2rem;opacity:.3;"></i>
                                No drug types yet. Add your first type on the left.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

{{-- ── Edit Modals ── --}}
@foreach($types as $type)
<div class="modal fade" id="editModal{{ $type->id }}" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header py-3">
                <h6 class="modal-title fw-bold">Edit Drug Type</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('drug-types.update', $type) }}" method="POST">
                @csrf @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:.83rem;">
                            Type Name <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="name"
                               class="form-control"
                               value="{{ $type->name }}" required>
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-semibold" style="font-size:.83rem;">Description</label>
                        <input type="text" name="description"
                               class="form-control"
                               value="{{ $type->description }}">
                    </div>
                </div>
                <div class="modal-footer py-2">
                    <button type="button" class="btn btn-sm btn-outline-secondary"
                            data-bs-dismiss="modal">{{ __('common.cancel') }}</button>
                    <button type="submit" class="btn btn-sm btn-primary">
                        <i class="bi bi-check-lg me-1"></i>{{ __('drug.update_type') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

@endsection
