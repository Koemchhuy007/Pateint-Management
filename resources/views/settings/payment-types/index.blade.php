@extends('layouts.app')
@section('title', 'Payment Types — Settings')

@section('breadcrumb')
<li class="breadcrumb-item active">Settings</li>
<li class="breadcrumb-item active">Payment Types</li>
@endsection

@section('content')
<div class="row g-4 align-items-start">

    @include('settings._sidebar')

    <div class="col-lg-10 col-md-9">

        <h5 class="fw-bold mb-4" style="color:#1e293b;">
            <i class="bi bi-credit-card-fill me-2 text-primary"></i>{{ __('settings.payment_types') }}
        </h5>

        <div class="row g-4 align-items-start">

            {{-- Add form --}}
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <i class="bi bi-plus-circle me-2 text-success"></i>{{ __('settings.payment_add') }}
                    </div>
                    <div class="card-body">
                        <form action="{{ route('settings.payment-types.store') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label fw-semibold" style="font-size:.83rem;">
                                    {{ __('common.name') }} <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="name"
                                       class="form-control @error('name') is-invalid @enderror"
                                       value="{{ old('name') }}"
                                       placeholder="e.g. Cash, Insurance, Card…" required>
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold" style="font-size:.83rem;">{{ __('common.description') }}</label>
                                <input type="text" name="description"
                                       class="form-control"
                                       value="{{ old('description') }}"
                                       placeholder="Optional description">
                            </div>
                            <button type="submit" class="btn btn-primary btn-sm w-100">
                                <i class="bi bi-plus-lg me-1"></i>{{ __('drug.add_type') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- List --}}
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <i class="bi bi-list-ul me-2"></i>{{ __('settings.all_payment_types') }}
                        <span class="badge bg-secondary ms-1" style="font-size:.7rem;">{{ $paymentTypes->count() }}</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" style="font-size:.88rem;">
                            <thead style="background:#f8fafc;">
                                <tr>
                                    <th class="ps-4">{{ __('common.name') }}</th>
                                    <th>{{ __('common.description') }}</th>
                                    <th style="width:80px;" class="text-center">{{ __('field.status') }}</th>
                                    <th style="width:110px;" class="text-end pe-4">{{ __('common.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($paymentTypes as $pt)
                                <tr>
                                    <td class="ps-4 fw-semibold" style="color:#1e293b;">{{ $pt->name }}</td>
                                    <td class="text-muted">{{ $pt->description ?: '—' }}</td>
                                    <td class="text-center">
                                        @if($pt->is_active)
                                            <span class="badge" style="background:#dcfce7;color:#15803d;font-size:.72rem;">Active</span>
                                        @else
                                            <span class="badge bg-secondary" style="font-size:.72rem;">Inactive</span>
                                        @endif
                                    </td>
                                    <td class="text-end pe-4">
                                        <button type="button" class="btn btn-sm btn-outline-primary"
                                                data-bs-toggle="modal"
                                                data-bs-target="#editPT{{ $pt->id }}">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <form action="{{ route('settings.payment-types.destroy', $pt) }}" method="POST"
                                              class="d-inline"
                                              onsubmit="return confirm('Delete \'{{ addslashes($pt->name) }}\'?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-5 text-muted">
                                        <i class="bi bi-credit-card d-block mb-2" style="font-size:2rem;opacity:.3;"></i>
                                        {{ __('settings.no_payment_types') }}
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
@foreach($paymentTypes as $pt)
<div class="modal fade" id="editPT{{ $pt->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header py-3">
                <h6 class="modal-title fw-bold">{{ __('settings.payment_edit') }}</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('settings.payment-types.update', $pt) }}" method="POST">
                @csrf @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:.83rem;">Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{ $pt->name }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:.83rem;">Description</label>
                        <input type="text" name="description" class="form-control" value="{{ $pt->description }}">
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" id="ptActive{{ $pt->id }}"
                               {{ $pt->is_active ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold" for="ptActive{{ $pt->id }}" style="font-size:.83rem;">
                            Active
                        </label>
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
