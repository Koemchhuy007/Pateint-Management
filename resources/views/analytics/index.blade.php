@extends('layouts.app')

@section('title', 'System Analytics')

@section('breadcrumb')
<li class="breadcrumb-item active">Analytics</li>
@endsection

@push('styles')
<style>
/* ── KPI cards ── */
.kpi-card {
    background: #fff;
    border-radius: .5rem;
    box-shadow: 0 1px 3px rgba(0,0,0,.08);
    padding: 20px 22px;
    display: flex;
    flex-direction: column;
    gap: 4px;
}
.kpi-value { font-size: 2rem; font-weight: 700; line-height: 1.1; }
.kpi-label { font-size: .78rem; color: #94a3b8; text-transform: uppercase; letter-spacing: .7px; font-weight: 600; }
.kpi-sub   { font-size: .82rem; color: #64748b; margin-top: 2px; }
.kpi-icon  { font-size: 1.6rem; margin-bottom: 4px; }

/* ── Chart cards ── */
.chart-card {
    background: #fff;
    border-radius: .5rem;
    box-shadow: 0 1px 3px rgba(0,0,0,.08);
    padding: 18px 20px;
}
.chart-title {
    font-size: .82rem;
    font-weight: 700;
    color: #475569;
    text-transform: uppercase;
    letter-spacing: .7px;
    margin-bottom: 14px;
}

/* ── Role badge in page header ── */
.role-badge {
    font-size: .72rem;
    font-weight: 700;
    padding: 3px 10px;
    border-radius: 20px;
    letter-spacing: .4px;
}
</style>
@endpush

@section('content')

{{-- ── Page header ── --}}
<div class="d-flex align-items-center gap-3 mb-4 flex-wrap">
    <div>
        <h4 class="mb-0 fw-bold">
            <i class="bi bi-graph-up-arrow me-2" style="color:#0891b2;"></i>System Analytics
        </h4>
        <div class="text-muted" style="font-size:.82rem;">{{ now()->format('F Y') }} overview</div>
    </div>
    @if(auth()->user()->isSuper())
    <span class="role-badge" style="background:#fef3c7;color:#b45309;">
        <i class="bi bi-shield-lock-fill me-1"></i>Super Admin — Platform View
    </span>
    @else
    <span class="role-badge" style="background:#dbeafe;color:#1d4ed8;">
        <i class="bi bi-person-badge-fill me-1"></i>System Admin — Tenant View
    </span>
    @endif
</div>

{{-- ══════════════════════════════
     KPI CARDS
══════════════════════════════ --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="kpi-card">
            <i class="kpi-icon bi bi-people-fill" style="color:#2563eb;"></i>
            <div class="kpi-value" style="color:#2563eb;">{{ number_format($totalPatients) }}</div>
            <div class="kpi-label">Total Patients</div>
            <div class="kpi-sub"><strong>{{ number_format($activeCases) }}</strong> active cases</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="kpi-card">
            <i class="kpi-icon bi bi-clipboard-pulse" style="color:#059669;"></i>
            <div class="kpi-value" style="color:#059669;">{{ number_format($visitsThisMonth) }}</div>
            <div class="kpi-label">Visits This Month</div>
            <div class="kpi-sub"><strong>{{ number_format($visitsThisWeek) }}</strong> last 7 days</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="kpi-card">
            <i class="kpi-icon bi bi-cash-stack" style="color:#d97706;"></i>
            <div class="kpi-value" style="color:#d97706;">${{ number_format($revenueThisMonth, 2) }}</div>
            <div class="kpi-label">Revenue This Month</div>
            <div class="kpi-sub">${{ number_format($totalBilled, 2) }} billed all time</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="kpi-card">
            <i class="kpi-icon bi bi-exclamation-circle" style="color:{{ $outstanding > 0 ? '#dc2626' : '#059669' }};"></i>
            <div class="kpi-value" style="color:{{ $outstanding > 0 ? '#dc2626' : '#059669' }};">
                ${{ number_format($outstanding, 2) }}
            </div>
            <div class="kpi-label">Outstanding</div>
            <div class="kpi-sub">${{ number_format($totalPaid, 2) }} collected total</div>
        </div>
    </div>
</div>

{{-- ══════════════════════════════
     CHARTS ROW
══════════════════════════════ --}}
<div class="row g-3 mb-4">

    {{-- Visits / 30 days line chart --}}
    <div class="col-md-8">
        <div class="chart-card">
            <div class="chart-title">
                <i class="bi bi-activity me-1" style="color:#2563eb;"></i>
                Visits — Last 30 Days
            </div>
            <canvas id="visitsChart" height="90"></canvas>
        </div>
    </div>

    {{-- OPD vs IPD doughnut --}}
    <div class="col-md-4">
        <div class="chart-card h-100 d-flex flex-column">
            <div class="chart-title">
                <i class="bi bi-pie-chart me-1" style="color:#7c3aed;"></i>
                Visit Types — This Month
            </div>
            <div class="flex-grow-1 d-flex align-items-center justify-content-center">
                <div style="position:relative;width:180px;height:180px;">
                    <canvas id="typeChart"></canvas>
                </div>
            </div>
            <div class="d-flex justify-content-center gap-4 mt-3" style="font-size:.8rem;">
                <span><span style="display:inline-block;width:12px;height:12px;border-radius:2px;background:#0ea5e9;margin-right:4px;"></span>OPD {{ $opdCount }}</span>
                <span><span style="display:inline-block;width:12px;height:12px;border-radius:2px;background:#8b5cf6;margin-right:4px;"></span>IPD {{ $ipdCount }}</span>
            </div>
        </div>
    </div>
</div>

{{-- Revenue chart --}}
<div class="chart-card mb-4">
    <div class="chart-title">
        <i class="bi bi-bar-chart me-1" style="color:#d97706;"></i>
        Revenue — Last 30 Days ($)
    </div>
    <canvas id="revenueChart" height="60"></canvas>
</div>

{{-- ══════════════════════════════
     RECENT ACTIVITY
══════════════════════════════ --}}
<div class="row g-3 mb-4">

    {{-- Recent visits --}}
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-clipboard-check me-2"></i>Recent Visits
            </div>
            <div class="card-body p-0">
                @if($recentVisits->isEmpty())
                <p class="text-center text-muted py-4 mb-0">No visits recorded yet.</p>
                @else
                <table class="table table-hover align-middle mb-0" style="font-size:.82rem;">
                    <thead class="table-light">
                        <tr>
                            <th>Patient</th>
                            <th class="text-center">Type</th>
                            <th>Doctor</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentVisits as $v)
                        <tr>
                            <td>{{ $v->patient?->full_name ?? '—' }}</td>
                            <td class="text-center">
                                @if($v->visit_type === 'OPD')
                                <span class="badge" style="background:#0ea5e9;">OPD</span>
                                @elseif($v->visit_type === 'IPD')
                                <span class="badge" style="background:#8b5cf6;">IPD</span>
                                @else
                                <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="text-muted">{{ $v->doctor_name ?: '—' }}</td>
                            <td class="text-muted">{{ $v->visit_date->format('d/m/Y') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @endif
            </div>
        </div>
    </div>

    {{-- Recent invoices --}}
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-receipt-cutoff me-2"></i>Recent Invoices
            </div>
            <div class="card-body p-0">
                @if($recentInvoices->isEmpty())
                <p class="text-center text-muted py-4 mb-0">No invoices yet.</p>
                @else
                <table class="table table-hover align-middle mb-0" style="font-size:.82rem;">
                    <thead class="table-light">
                        <tr>
                            <th>Invoice #</th>
                            <th>Patient</th>
                            <th class="text-end">Billed</th>
                            <th class="text-end">Paid</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentInvoices as $inv)
                        @php $billed = $inv->total_pay; @endphp
                        <tr>
                            <td class="text-muted" style="font-size:.75rem;">{{ $inv->invoice_number }}</td>
                            <td>{{ $inv->patient?->full_name ?? '—' }}</td>
                            <td class="text-end">${{ number_format($billed, 2) }}</td>
                            <td class="text-end
                                {{ $inv->money_paid >= $billed ? 'text-success' : 'text-danger' }}">
                                ${{ number_format($inv->money_paid, 2) }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- ══════════════════════════════
     PER-CLIENT SUMMARY (super admin only)
══════════════════════════════ --}}
@if($clientSummary !== null)
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-buildings me-2"></i>Per-Client Summary</span>
        <a href="{{ route('admin.clients.index') }}" class="btn btn-sm btn-outline-secondary">
            Manage Clients
        </a>
    </div>
    <div class="card-body p-0">
        @if($clientSummary->isEmpty())
        <p class="text-center text-muted py-4 mb-0">No clients registered yet.</p>
        @else
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Client</th>
                        <th class="text-center">Users</th>
                        <th class="text-center">Patients</th>
                        <th class="text-center">Active Cases</th>
                        <th class="text-center">Status</th>
                        <th style="width:100px" class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($clientSummary as $c)
                    <tr>
                        <td class="fw-semibold">{{ $c->name }}</td>
                        <td class="text-center">
                            <span class="badge bg-secondary">{{ $c->users_count }}</span>
                        </td>
                        <td class="text-center">{{ number_format($c->patients_count) }}</td>
                        <td class="text-center">
                            @if($c->active_cases_count > 0)
                            <span class="badge" style="background:#059669;">{{ $c->active_cases_count }}</span>
                            @else
                            <span class="text-muted">0</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($c->is_active)
                            <span class="badge" style="background:#059669;">Active</span>
                            @else
                            <span class="badge bg-secondary">Inactive</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <a href="{{ route('admin.clients.edit', $c) }}"
                               class="btn btn-sm btn-outline-primary border-0" title="Edit client">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <a href="{{ route('admin.users.index', ['client_id' => $c->id]) }}"
                               class="btn btn-sm btn-outline-secondary border-0" title="View users">
                                <i class="bi bi-people"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>
@endif

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const visitLabels  = @json($visitLabels);
const visitData    = @json($visitData);
const revenueData  = @json($revenueData);
const opdCount     = @json($opdCount);
const ipdCount     = @json($ipdCount);

// ── Visits line chart ────────────────────────────────────
new Chart(document.getElementById('visitsChart'), {
    type: 'line',
    data: {
        labels: visitLabels,
        datasets: [{
            label: 'Visits',
            data: visitData,
            borderColor: '#2563eb',
            backgroundColor: 'rgba(37,99,235,.08)',
            borderWidth: 2,
            pointRadius: 3,
            pointHoverRadius: 5,
            fill: true,
            tension: 0.35,
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            x: { grid: { display: false }, ticks: { font: { size: 11 }, maxRotation: 0,
                callback: (val, i) => i % 5 === 0 ? visitLabels[i] : '' } },
            y: { beginAtZero: true, ticks: { stepSize: 1, font: { size: 11 } },
                grid: { color: '#f1f5f9' } }
        }
    }
});

// ── OPD / IPD doughnut ────────────────────────────────────
new Chart(document.getElementById('typeChart'), {
    type: 'doughnut',
    data: {
        labels: ['OPD', 'IPD'],
        datasets: [{
            data: [opdCount, ipdCount],
            backgroundColor: ['#0ea5e9', '#8b5cf6'],
            borderWidth: 2,
            borderColor: '#fff',
        }]
    },
    options: {
        cutout: '65%',
        plugins: {
            legend: { display: false },
            tooltip: {
                callbacks: {
                    label: ctx => ` ${ctx.label}: ${ctx.raw} visits`
                }
            }
        }
    }
});

// ── Revenue bar chart ────────────────────────────────────
new Chart(document.getElementById('revenueChart'), {
    type: 'bar',
    data: {
        labels: visitLabels,
        datasets: [{
            label: 'Revenue ($)',
            data: revenueData,
            backgroundColor: 'rgba(217,119,6,.6)',
            borderColor: '#d97706',
            borderWidth: 1,
            borderRadius: 3,
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            x: { grid: { display: false }, ticks: { font: { size: 11 }, maxRotation: 0,
                callback: (val, i) => i % 5 === 0 ? visitLabels[i] : '' } },
            y: { beginAtZero: true, ticks: { font: { size: 11 } },
                grid: { color: '#f1f5f9' } }
        }
    }
});
</script>
@endpush
