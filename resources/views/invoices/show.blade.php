@extends('layouts.app')

@section('title', 'Invoice — ' . $patient->full_name)

@push('styles')
<style>
    /* ── Invoice page layout ── */
    .inv-panel {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: .5rem;
        overflow: hidden;
    }
    .inv-panel-header {
        background: var(--topbar-bg);
        color: #fff;
        padding: 10px 16px;
        font-size: .875rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    /* Items table */
    .inv-table { width: 100%; border-collapse: collapse; font-size: .875rem; }
    .inv-table thead th {
        background: #f8fafc;
        border-bottom: 2px solid #e2e8f0;
        padding: 8px 10px;
        font-weight: 600;
        color: #475569;
        white-space: nowrap;
    }
    .inv-table tbody td {
        border-bottom: 1px solid #f1f5f9;
        padding: 6px 10px;
        vertical-align: middle;
    }
    .inv-table tbody tr:last-child td { border-bottom: none; }
    .inv-table tfoot td {
        padding: 6px 10px;
        font-size: .875rem;
    }
    .inv-table tfoot tr:last-child td {
        font-weight: 700;
        font-size: .925rem;
        border-top: 2px solid #e2e8f0;
        padding-top: 8px;
    }

    /* Service picker */
    .service-list {
        max-height: 260px;
        overflow-y: auto;
        border: 1px solid #e2e8f0;
        border-radius: .375rem;
    }
    .service-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 7px 12px;
        cursor: pointer;
        border-bottom: 1px solid #f1f5f9;
        transition: background .1s;
        font-size: .85rem;
        gap: 8px;
    }
    .service-item:last-child { border-bottom: none; }
    .service-item:hover { background: #eff6ff; }
    .service-item .svc-name { flex: 1; color: #1e293b; }
    .service-item .svc-price { color: #64748b; font-size: .8rem; white-space: nowrap; }
    .service-group-label {
        padding: 5px 12px;
        font-size: .75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .05em;
        color: #94a3b8;
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        position: sticky;
        top: 0;
    }
    .no-services-msg {
        padding: 24px;
        text-align: center;
        color: #94a3b8;
        font-size: .85rem;
    }

    /* Payment list */
    .plist-table { width: 100%; border-collapse: collapse; font-size: .875rem; }
    .plist-table thead th {
        background: var(--topbar-bg);
        color: #fff;
        padding: 9px 12px;
        font-weight: 600;
        font-size: .8rem;
    }
    .plist-table tbody td {
        padding: 8px 12px;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: top;
    }
    .plist-table tbody tr.inv-group-first td { border-top: 1px solid #e2e8f0; }
    .plist-table tfoot td {
        padding: 8px 12px;
        font-weight: 700;
        border-top: 2px solid #e2e8f0;
        background: #f8fafc;
    }

    /* Small form controls inside table */
    .inv-table .form-control-sm { height: 30px; font-size: .82rem; }

    /* Totals alignment */
    .totals-label { text-align: right; color: #475569; padding-right: 8px; }
    .totals-value { text-align: right; font-weight: 600; color: #0f172a; min-width: 100px; }
</style>
@endpush

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('invoices.index') }}">Invoice</a></li>
    <li class="breadcrumb-item active">{{ $patient->full_name }}</li>
@endsection

@section('content')

{{-- ── Patient header ── --}}
<div class="d-flex align-items-center gap-3 mb-3">
    <div>
        <h5 class="mb-0 fw-bold">{{ $patient->full_name }}</h5>
        <small class="text-muted">
            <code>{{ $patient->patient_id }}</code>
            @if($activeVisit)
                &nbsp;&bull;&nbsp;
                <span class="badge bg-{{ $activeVisit->visit_type === 'IPD' ? 'warning text-dark' : 'info text-dark' }}">
                    {{ $activeVisit->visit_type }}
                </span>
                &nbsp;Visit: {{ $activeVisit->visit_date->format('d/m/Y') }}
                &nbsp;&bull;&nbsp;Dr. {{ $activeVisit->doctor_name }}
            @endif
        </small>
    </div>
    <a href="{{ route('invoices.index') }}" class="btn btn-sm btn-outline-secondary ms-auto">
        <i class="bi bi-arrow-left me-1"></i>Back
    </a>
</div>

{{-- ── Main grid: Payment form + Service picker ── --}}
<div class="row g-3 mb-3">

    {{-- LEFT: Payment Information --}}
    <div class="col-lg-7">
        <div class="inv-panel h-100">
            <div class="inv-panel-header">
                <i class="bi bi-receipt-cutoff"></i> Payment information
            </div>
            <div class="p-3">

                {{-- Type of payment --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold" style="font-size:.85rem;">
                        Type of Payment <span class="text-danger">*</span>
                    </label>
                    <select id="paymentTypeSelect" class="form-select form-select-sm">
                        <option value="">— Select —</option>
                        @foreach($paymentTypes as $pt)
                            <option value="{{ $pt->id }}">{{ $pt->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Items table --}}
                <div class="table-responsive mb-2" style="min-height:120px;">
                    <table class="inv-table">
                        <thead>
                            <tr>
                                <th style="width:36px" class="text-center">ណ.ស</th>
                                <th>Service</th>
                                <th style="width:70px" class="text-center">Qty</th>
                                <th style="width:110px" class="text-end">Price</th>
                                <th style="width:120px" class="text-center">Discount %</th>
                                <th style="width:44px"></th>
                            </tr>
                        </thead>
                        <tbody id="itemsBody">
                            <tr id="emptyRow">
                                <td colspan="6" class="text-center text-muted py-4" style="font-size:.85rem;">
                                    <i class="bi bi-arrow-right-circle me-1"></i>
                                    Click a service on the right to add it
                                </td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" class="totals-label">Actual Amount</td>
                                <td class="totals-value" id="totalActual">0</td>
                                <td colspan="2"></td>
                            </tr>
                            <tr>
                                <td colspan="3" class="totals-label">Discount</td>
                                <td class="totals-value text-danger" id="totalDiscount">0</td>
                                <td colspan="2"></td>
                            </tr>
                            <tr>
                                <td colspan="3" class="totals-label">Total Pay</td>
                                <td class="totals-value text-primary" id="totalPay">0</td>
                                <td colspan="2"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                {{-- Action buttons --}}
                <div class="d-flex gap-2 justify-content-end pt-2 border-top">
                    <button type="button" class="btn btn-primary btn-sm" onclick="saveInvoice()">
                        <i class="bi bi-floppy me-1"></i>Save
                    </button>
                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="resetForm()">
                        <i class="bi bi-plus-circle me-1"></i>New Invoice
                    </button>
                    <button type="button" class="btn btn-outline-dark btn-sm" onclick="printInvoice()">
                        <i class="bi bi-printer me-1"></i>Set the paper
                    </button>
                </div>

            </div>
        </div>
    </div>

    {{-- RIGHT: Ward + Service picker + Remark --}}
    <div class="col-lg-5">
        <div class="inv-panel h-100">
            <div class="inv-panel-header">
                <i class="bi bi-grid-3x3-gap"></i> Services
            </div>
            <div class="p-3 d-flex flex-column gap-2">

                {{-- Ward dropdown --}}
                <div class="d-flex align-items-center gap-2">
                    <label class="fw-semibold text-nowrap" style="font-size:.85rem; min-width:38px;">Ward</label>
                    <select id="wardSelect" class="form-select form-select-sm" onchange="filterServices()">
                        <option value="">All Wards</option>
                        @foreach($serviceGroups as $sg)
                            <option value="{{ $sg->id }}">{{ $sg->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Search --}}
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" id="serviceSearch" class="form-control"
                           placeholder="Search service name…" oninput="filterServices()">
                </div>

                {{-- Service list --}}
                <div class="service-list" id="serviceList">
                    {{-- populated by JS --}}
                </div>

                {{-- Remark --}}
                <div>
                    <label class="form-label fw-semibold mb-1" style="font-size:.85rem;">Remark</label>
                    <textarea id="remarkInput" class="form-control form-control-sm"
                              rows="2" placeholder="Optional note…"></textarea>
                </div>

            </div>
        </div>
    </div>
</div>

{{-- ── Payment List ── --}}
<div class="inv-panel">
    <div class="inv-panel-header d-flex align-items-center justify-content-between">
        <span><i class="bi bi-list-ul me-2"></i>Payment list</span>
        <button type="button" class="btn btn-sm btn-light" onclick="window.print()" title="Print all">
            <i class="bi bi-printer"></i>
        </button>
    </div>

    @if($invoices->isEmpty())
        <div class="text-center py-4 text-muted" style="font-size:.875rem;">
            <i class="bi bi-inbox d-block mb-2 fs-4 opacity-25"></i>No invoices yet.
        </div>
    @else
        <div class="table-responsive">
            <table class="plist-table">
                <thead>
                    <tr>
                        <th style="width:190px">Invoice Information</th>
                        <th>Service</th>
                        <th style="width:110px" class="text-end">Price</th>
                        <th style="width:110px" class="text-end">Money paid</th>
                        <th style="width:130px" class="text-center">Type of Payment</th>
                        <th style="width:90px" class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @php $grandTotal = 0; $grandPaid = 0; @endphp
                    @foreach($invoices as $invoice)
                        @php
                            $itemCount   = $invoice->items->count();
                            $totalPay    = $invoice->total_pay;
                            $grandTotal += $totalPay;
                            $grandPaid  += (float) $invoice->money_paid;
                        @endphp
                        @foreach($invoice->items as $idx => $item)
                            <tr class="{{ $idx === 0 ? 'inv-group-first' : '' }}">
                                {{-- Invoice info cell (only on first item row) --}}
                                @if($idx === 0)
                                    <td rowspan="{{ $itemCount }}" style="vertical-align:top; border-right:1px solid #e2e8f0;">
                                        <div class="text-muted" style="font-size:.78rem;">Invoice Date</div>
                                        <div class="fw-semibold mb-1" style="font-size:.85rem;">
                                            {{ $invoice->invoice_date->format('d/m/Y') }}
                                        </div>
                                        <div class="text-muted" style="font-size:.78rem;">Invoice</div>
                                        <div class="mb-1" style="font-size:.82rem;">
                                            <code>{{ $invoice->invoice_number }}</code>
                                        </div>
                                        <div class="text-muted" style="font-size:.78rem;">Cashier</div>
                                        <div class="mb-2" style="font-size:.85rem;">
                                            {{ $invoice->cashier->name ?? '—' }}
                                        </div>
                                        <div class="d-flex gap-1 flex-wrap">
                                            <button class="btn btn-outline-secondary btn-sm p-1"
                                                    title="Print"
                                                    onclick="printSingleInvoice({{ $invoice->id }})">
                                                <i class="bi bi-printer" style="font-size:.8rem;"></i>
                                            </button>
                                            <button class="btn btn-outline-danger btn-sm p-1"
                                                    title="Delete"
                                                    onclick="deleteInvoice({{ $invoice->id }}, this)">
                                                <i class="bi bi-trash" style="font-size:.8rem;"></i>
                                            </button>
                                        </div>
                                    </td>
                                @endif

                                {{-- Service item --}}
                                <td style="font-size:.85rem;">
                                    {{ $item->service_name }}
                                    @if($item->discount_pct > 0)
                                        <span class="badge bg-warning text-dark ms-1"
                                              style="font-size:.7rem;">
                                            -{{ number_format($item->discount_pct, 0) }}%
                                        </span>
                                    @endif
                                </td>

                                {{-- Price, paid, type (only on first item row) --}}
                                @if($idx === 0)
                                    <td class="text-end fw-semibold" rowspan="{{ $itemCount }}"
                                        style="vertical-align:middle; border-left:1px solid #f1f5f9;">
                                        {{ number_format($totalPay, 0) }}
                                    </td>
                                    <td class="text-end" rowspan="{{ $itemCount }}"
                                        style="vertical-align:middle;">
                                        {{ number_format((float)$invoice->money_paid, 0) }}
                                    </td>
                                    <td class="text-center" rowspan="{{ $itemCount }}"
                                        style="vertical-align:middle;">
                                        <span class="badge bg-primary" style="font-size:.75rem;">
                                            {{ $invoice->paymentType->name ?? '—' }}
                                        </span>
                                    </td>
                                    <td rowspan="{{ $itemCount }}" style="vertical-align:middle;"></td>
                                @endif
                            </tr>
                        @endforeach
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="2" class="text-end">Total</td>
                        <td class="text-end">{{ number_format($grandTotal, 0) }}</td>
                        <td class="text-end">{{ number_format($grandPaid, 0) }}</td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    @endif
</div>

@endsection

@php
    $serviceGroupsJson = $serviceGroups->map(function ($g) {
        return [
            'id'       => $g->id,
            'name'     => $g->name,
            'services' => $g->services->map(function ($s) {
                return [
                    'id'    => $s->id,
                    'name'  => $s->name,
                    'price' => (float) $s->price,
                ];
            })->values(),
        ];
    })->values();
@endphp

@push('scripts')
<script>
/* ════════════════════════════════════════════════
   Service groups data (from PHP)
════════════════════════════════════════════════ */
const serviceGroups = @json($serviceGroupsJson);

const storeUrl = "{{ route('invoices.store', $patient) }}";
const deleteUrl = (id) => `/invoices/${id}`;
const csrfToken = document.querySelector('meta[name=csrf-token]').content;

/* ════════════════════════════════════════════════
   Invoice items state
════════════════════════════════════════════════ */
let items = [];
let nextId = 1;

function addService(serviceId, serviceName, price) {
    items.push({ _id: nextId++, serviceId, serviceName, qty: 1, unitPrice: price, discountPct: 0 });
    renderItems();
}

function removeItem(tempId) {
    items = items.filter(i => i._id !== tempId);
    renderItems();
}

function renderItems() {
    const tbody = document.getElementById('itemsBody');
    const emptyRow = document.getElementById('emptyRow');

    if (items.length === 0) {
        tbody.innerHTML = '';
        tbody.appendChild(emptyRow);
        recalculate();
        return;
    }

    tbody.innerHTML = '';
    items.forEach((item, index) => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td class="text-center text-muted" style="font-size:.8rem;">${index + 1}</td>
            <td style="font-size:.875rem;">${escHtml(item.serviceName)}</td>
            <td>
                <input type="number" class="form-control form-control-sm text-center"
                    value="${item.qty}" min="1" style="width:60px;"
                    onchange="updateField(${item._id},'qty',this.value)">
            </td>
            <td>
                <input type="number" class="form-control form-control-sm text-end"
                    value="${item.unitPrice}" min="0" step="1"
                    onchange="updateField(${item._id},'unitPrice',this.value)">
            </td>
            <td>
                <div class="input-group input-group-sm">
                    <span class="input-group-text" style="font-size:.78rem;">%</span>
                    <input type="number" class="form-control form-control-sm text-end"
                        value="${item.discountPct}" min="0" max="100" step="0.01"
                        onchange="updateField(${item._id},'discountPct',this.value)">
                </div>
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-danger btn-sm p-1"
                        onclick="removeItem(${item._id})">
                    <i class="bi bi-trash" style="font-size:.8rem;"></i>
                </button>
            </td>`;
        tbody.appendChild(tr);
    });
    recalculate();
}

function updateField(id, field, val) {
    const item = items.find(i => i._id === id);
    if (!item) return;
    if (field === 'qty')          item.qty          = Math.max(1, parseInt(val) || 1);
    if (field === 'unitPrice')    item.unitPrice    = parseFloat(val) || 0;
    if (field === 'discountPct')  item.discountPct  = Math.min(100, Math.max(0, parseFloat(val) || 0));
    recalculate();
}

function recalculate() {
    let actual = 0, discount = 0;
    items.forEach(i => {
        const line = i.qty * i.unitPrice;
        actual   += line;
        discount += line * i.discountPct / 100;
    });
    setText('totalActual',   fmt(actual));
    setText('totalDiscount', fmt(discount));
    setText('totalPay',      fmt(actual - discount));
}

/* ════════════════════════════════════════════════
   Service picker
════════════════════════════════════════════════ */
function filterServices() {
    const wardId = parseInt(document.getElementById('wardSelect').value) || null;
    const q      = document.getElementById('serviceSearch').value.trim().toLowerCase();
    const list   = document.getElementById('serviceList');
    list.innerHTML = '';

    let anyShown = false;
    serviceGroups.forEach(group => {
        if (wardId && group.id !== wardId) return;

        const matched = group.services.filter(s =>
            !q || s.name.toLowerCase().includes(q)
        );
        if (!matched.length) return;

        // Group label
        const label = document.createElement('div');
        label.className = 'service-group-label';
        label.textContent = group.name;
        list.appendChild(label);

        matched.forEach(svc => {
            const el = document.createElement('div');
            el.className = 'service-item';
            el.innerHTML = `<span class="svc-name">${escHtml(svc.name)}</span>
                            <span class="svc-price">${fmt(svc.price)}</span>`;
            el.onclick = () => addService(svc.id, svc.name, svc.price);
            list.appendChild(el);
            anyShown = true;
        });
    });

    if (!anyShown) {
        const msg = document.createElement('div');
        msg.className = 'no-services-msg';
        msg.textContent = 'No services found.';
        list.appendChild(msg);
    }
}

/* ════════════════════════════════════════════════
   Save invoice
════════════════════════════════════════════════ */
async function saveInvoice() {
    const paymentTypeId = document.getElementById('paymentTypeSelect').value;
    if (!paymentTypeId) {
        alert('Please select a Type of Payment.');
        return;
    }
    if (items.length === 0) {
        alert('Please add at least one service.');
        return;
    }

    const wardSel   = document.getElementById('wardSelect');
    const wardLabel = wardSel.options[wardSel.selectedIndex]?.text || '';

    const payload = {
        payment_type_id: parseInt(paymentTypeId),
        ward:            wardLabel !== 'All Wards' ? wardLabel : null,
        remark:          document.getElementById('remarkInput').value.trim() || null,
        invoice_date:    new Date().toISOString().split('T')[0],
        money_paid:      0,
        items: items.map(i => ({
            service_id:   i.serviceId,
            service_name: i.serviceName,
            quantity:     i.qty,
            unit_price:   i.unitPrice,
            discount_pct: i.discountPct,
        })),
    };

    try {
        const res = await fetch(storeUrl, {
            method:  'POST',
            headers: {
                'Content-Type':  'application/json',
                'X-CSRF-TOKEN':  csrfToken,
                'Accept':        'application/json',
            },
            body: JSON.stringify(payload),
        });

        const data = await res.json();

        if (res.ok && data.success) {
            location.reload();
        } else {
            const msg = data.message || (data.errors ? Object.values(data.errors).flat().join('\n') : 'Save failed.');
            alert(msg);
        }
    } catch (e) {
        alert('Network error. Please try again.');
    }
}

/* ════════════════════════════════════════════════
   Delete invoice
════════════════════════════════════════════════ */
async function deleteInvoice(id, btn) {
    if (!confirm('Delete this invoice? This cannot be undone.')) return;

    btn.disabled = true;
    try {
        const res  = await fetch(deleteUrl(id), {
            method:  'DELETE',
            headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
        });
        const data = await res.json();
        if (data.success) location.reload();
        else alert('Delete failed.');
    } catch {
        alert('Network error.');
        btn.disabled = false;
    }
}

/* ════════════════════════════════════════════════
   Print helpers
════════════════════════════════════════════════ */
function printInvoice()       { window.print(); }
function printSingleInvoice() { window.print(); }

/* ════════════════════════════════════════════════
   Reset form
════════════════════════════════════════════════ */
function resetForm() {
    items = [];
    document.getElementById('paymentTypeSelect').value = '';
    document.getElementById('wardSelect').value        = '';
    document.getElementById('serviceSearch').value     = '';
    document.getElementById('remarkInput').value       = '';
    renderItems();
    filterServices();
}

/* ════════════════════════════════════════════════
   Utilities
════════════════════════════════════════════════ */
function fmt(n)        { return new Intl.NumberFormat().format(Math.round(n)); }
function setText(id,v) { document.getElementById(id).textContent = v; }
function escHtml(str)  {
    return str.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

/* ════════════════════════════════════════════════
   Init
════════════════════════════════════════════════ */
document.addEventListener('DOMContentLoaded', () => {
    renderItems();
    filterServices();
});
</script>
@endpush
