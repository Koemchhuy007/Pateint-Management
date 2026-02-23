@extends('layouts.app')

@section('title', 'Drugstore')

@section('breadcrumb')
<li class="breadcrumb-item active">Drugstore</li>
@endsection

@push('styles')
<style>
.stock-badge {
    display: inline-flex; align-items: center; gap: 4px;
    padding: 3px 10px;
    border-radius: 20px;
    font-size: .78rem;
    font-weight: 600;
}
.stock-ok   { background: #dcfce7; color: #15803d; }
.stock-low  { background: #fef9c3; color: #92400e; }
.stock-zero { background: #fee2e2; color: #dc2626; }
</style>
@endpush

@section('content')

{{-- ── Header ── --}}
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div>
        <h5 class="fw-bold mb-0" style="color:#1e293b;">
            <i class="bi bi-capsule-pill me-2 text-success"></i>Drugstore
        </h5>
        <div class="text-muted mt-1" style="font-size:.82rem;">
            {{ $drugs->total() }} drug{{ $drugs->total() != 1 ? 's' : '' }} in inventory
        </div>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <a href="{{ route('drug-types.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-tags me-1"></i>Drug Types
        </a>
        <a href="{{ route('drugstore.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg me-1"></i>Add Drug
        </a>
    </div>
</div>

{{-- ── Filter bar ── --}}
<div class="card mb-4">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('drugstore.index') }}" class="row g-2 align-items-end">
            <div class="col-sm-5">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light border-end-0">
                        <i class="bi bi-search text-muted"></i>
                    </span>
                    <input type="text" name="search" class="form-control border-start-0"
                           placeholder="Search drug name…"
                           value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-sm-4">
                <select name="type_id" class="form-select form-select-sm">
                    <option value="">— All Types —</option>
                    @foreach($drugTypes as $type)
                        <option value="{{ $type->id }}" {{ request('type_id') == $type->id ? 'selected' : '' }}>
                            {{ $type->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm flex-fill">
                    <i class="bi bi-funnel me-1"></i>Filter
                </button>
                @if(request('search') || request('type_id'))
                    <a href="{{ route('drugstore.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-x-lg"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

{{-- ── Drug table ── --}}
<div class="card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" style="font-size:.88rem;">
            <thead style="background:#f8fafc;">
                <tr>
                    <th style="width:50px;" class="ps-4">#</th>
                    <th>Drug Name</th>
                    <th>Type</th>
                    <th style="width:100px;">Unit</th>
                    <th style="width:130px;" class="text-center">Stock Qty</th>
                    <th style="width:110px;" class="text-end pe-4">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($drugs as $drug)
                <tr>
                    <td class="ps-4 text-muted">{{ $drugs->firstItem() + $loop->index }}</td>
                    <td>
                        <div class="fw-semibold" style="color:#1e293b;">{{ $drug->name }}</div>
                        @if($drug->description)
                            <div class="text-muted" style="font-size:.78rem;">{{ Str::limit($drug->description, 60) }}</div>
                        @endif
                    </td>
                    <td>
                        @if($drug->drugType)
                        <span class="badge" style="background:#eff6ff;color:#2563eb;font-size:.75rem;">
                            {{ $drug->drugType->name }}
                        </span>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td class="text-muted">{{ $drug->unit ?: '—' }}</td>
                    <td class="text-center">
                        @if($drug->stock_quantity == 0)
                            <span class="stock-badge stock-zero">
                                <i class="bi bi-x-circle-fill" style="font-size:.7rem;"></i>
                                Out of Stock
                            </span>
                        @elseif($drug->stock_quantity <= 10)
                            <span class="stock-badge stock-low">
                                <i class="bi bi-exclamation-triangle-fill" style="font-size:.7rem;"></i>
                                {{ $drug->stock_quantity }}
                            </span>
                        @else
                            <span class="stock-badge stock-ok">
                                <i class="bi bi-check-circle-fill" style="font-size:.7rem;"></i>
                                {{ $drug->stock_quantity }}
                            </span>
                        @endif
                    </td>
                    <td class="text-end pe-4">
                        <a href="{{ route('drugstore.edit', $drug) }}"
                           class="btn btn-sm btn-outline-primary" title="Edit">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form action="{{ route('drugstore.destroy', $drug) }}" method="POST"
                              class="d-inline"
                              onsubmit="return confirm('Delete {{ addslashes($drug->name) }}?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-5 text-muted">
                        <i class="bi bi-capsule-pill d-block mb-2" style="font-size:2rem;opacity:.3;"></i>
                        No drugs found.
                        <a href="{{ route('drugstore.create') }}" class="ms-1">Add your first drug →</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($drugs->hasPages())
    <div class="card-footer bg-white d-flex justify-content-between align-items-center py-3">
        <div class="text-muted" style="font-size:.82rem;">
            Showing {{ $drugs->firstItem() }}–{{ $drugs->lastItem() }} of {{ $drugs->total() }}
        </div>
        {{ $drugs->links() }}
    </div>
    @endif
</div>

@endsection
