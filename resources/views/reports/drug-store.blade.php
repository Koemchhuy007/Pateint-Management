@extends('layouts.app')

@section('title', 'Drug Store Report')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('reports.index') }}">Reports</a></li>
    <li class="breadcrumb-item active">Drug Store Report</li>
@endsection

@section('content')
<div class="mb-3 d-flex align-items-center gap-2">
    <a href="{{ route('reports.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Back
    </a>
    <h5 class="fw-bold mb-0" style="color:#1e293b;">
        <i class="bi bi-box-seam-fill me-2" style="color:#d97706;"></i>Drug Store Report
    </h5>
</div>

{{-- Filter Form --}}
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('reports.drug-store') }}" class="row g-3 align-items-end">
            <div class="col-sm-5 col-md-4">
                <label class="form-label fw-semibold" style="font-size:.85rem;">Filter by Drug Type</label>
                <select name="drug_type_id" class="form-select">
                    <option value="">All Types</option>
                    @foreach($drugTypes as $type)
                        <option value="{{ $type->id }}" {{ (string)$typeId === (string)$type->id ? 'selected' : '' }}>
                            {{ $type->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary" style="background:#d97706;border-color:#d97706;">
                    <i class="bi bi-funnel me-1"></i>Filter
                </button>
            </div>
            <div class="col-auto">
                <button type="button" class="btn btn-outline-secondary" onclick="window.print()">
                    <i class="bi bi-printer me-1"></i>Print
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Summary Cards --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card text-center h-100">
            <div class="card-body py-3">
                <div style="font-size:1.6rem;font-weight:700;color:#1e293b;">{{ $summary['total_items'] }}</div>
                <div class="text-muted" style="font-size:.78rem;">Total Items</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card text-center h-100">
            <div class="card-body py-3">
                <div style="font-size:1.6rem;font-weight:700;color:#2563eb;">{{ number_format($summary['total_stock']) }}</div>
                <div class="text-muted" style="font-size:.78rem;">Total Stock Units</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card text-center h-100">
            <div class="card-body py-3">
                <div style="font-size:1.6rem;font-weight:700;color:#d97706;">{{ $summary['low_stock'] }}</div>
                <div class="text-muted" style="font-size:.78rem;">Low Stock</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card text-center h-100">
            <div class="card-body py-3">
                <div style="font-size:1.6rem;font-weight:700;color:#dc2626;">{{ $summary['out_of_stock'] }}</div>
                <div class="text-muted" style="font-size:.78rem;">Out of Stock</div>
            </div>
        </div>
    </div>
</div>

{{-- Results Table --}}
<div class="card">
    <div class="card-body p-0">
        @if($drugs->isEmpty())
            <div class="text-center text-muted py-5">
                <i class="bi bi-box" style="font-size:2.5rem; opacity:.35;"></i>
                <div class="mt-2">No drugs found.</div>
            </div>
        @else
        <div class="table-responsive">
            <table class="table table-hover mb-0" style="font-size:.84rem;">
                <thead style="background:#f8fafc;">
                    <tr>
                        <th class="ps-3">#</th>
                        <th>Drug Name</th>
                        <th>Type / Category</th>
                        <th>Unit</th>
                        <th class="text-end pe-3">Stock Quantity</th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($drugs as $i => $drug)
                    <tr>
                        <td class="ps-3 text-muted">{{ $i + 1 }}</td>
                        <td class="fw-semibold">{{ $drug->name }}</td>
                        <td class="text-muted">{{ $drug->drugType?->name ?? '—' }}</td>
                        <td class="text-muted">{{ $drug->unit ?: '—' }}</td>
                        <td class="text-end pe-3 fw-semibold
                            {{ $drug->stock_quantity === 0 ? 'text-danger' : ($drug->isLowStock() ? 'text-warning' : '') }}">
                            {{ number_format($drug->stock_quantity) }}
                        </td>
                        <td class="text-center">
                            @if($drug->stock_quantity === 0)
                                <span class="badge bg-danger">Out of Stock</span>
                            @elseif($drug->isLowStock())
                                <span class="badge" style="background:#d97706;">Low Stock</span>
                            @else
                                <span class="badge" style="background:#10b981;">In Stock</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot style="background:#f8fafc;">
                    <tr>
                        <th colspan="4" class="ps-3">Total</th>
                        <th class="text-end pe-3">{{ number_format($summary['total_stock']) }}</th>
                        <th></th>
                    </tr>
                </tfoot>
            </table>
        </div>
        @endif
    </div>
</div>

@push('styles')
<style>
@media print {
    .subnav, .topbar, .breadcrumb-bar, form, .btn, a.btn { display: none !important; }
    .page-content { padding: 0 !important; }
}
</style>
@endpush
@endsection
