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
        <button type="button" class="btn btn-sm btn-light" onclick="printAllInvoices()" title="Print all invoices">
            <i class="bi bi-printer me-1"></i><span style="font-size:.8rem;">Print All</span>
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

    // Serialised for client-side single-invoice print
    $invoicesData = $invoices->map(function ($inv) {
        return [
            'id'             => $inv->id,
            'invoice_number' => $inv->invoice_number,
            'invoice_date'   => $inv->invoice_date->format('d/m/Y'),
            'payment_type'   => $inv->paymentType?->name ?? '—',
            'cashier'        => $inv->cashier?->name ?? '—',
            'ward'           => $inv->ward,
            'remark'         => $inv->remark,
            'money_paid'     => (float) $inv->money_paid,
            'actual_amount'  => (float) $inv->actual_amount,
            'total_discount' => (float) $inv->total_discount,
            'total_pay'      => (float) $inv->total_pay,
            'items'          => $inv->items->map(function ($item) {
                return [
                    'service_name' => $item->service_name,
                    'quantity'     => (int)   $item->quantity,
                    'unit_price'   => (float) $item->unit_price,
                    'discount_pct' => (float) $item->discount_pct,
                    'subtotal'     => (float) ($item->unit_price * $item->quantity * (1 - $item->discount_pct / 100)),
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

/* ── Data for printing ── */
const invoicesData  = @json($invoicesData);
const _patientName  = @json($patient->full_name);
const _patientId    = @json($patient->patient_id);
const _appName      = @json(config('app.name'));
const _cashierName  = @json(auth()->user()->name ?? '');

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

/**
 * Print the current form as a DRAFT receipt (items not yet saved).
 */
function printInvoice() {
    if (items.length === 0) {
        alert('Please add at least one service before printing.');
        return;
    }

    const ptSel       = document.getElementById('paymentTypeSelect');
    const paymentType = ptSel.options[ptSel.selectedIndex]?.text || '—';
    const wardSel     = document.getElementById('wardSelect');
    const ward        = wardSel.options[wardSel.selectedIndex]?.text || '';
    const remark      = document.getElementById('remarkInput').value.trim();

    let actual = 0, discount = 0;
    items.forEach(i => {
        const line  = i.qty * i.unitPrice;
        actual     += line;
        discount   += line * i.discountPct / 100;
    });

    openReceiptPopup({
        invoice_number: 'DRAFT',
        invoice_date:   new Date().toLocaleDateString('en-GB'),
        payment_type:   (paymentType && paymentType !== '— Select —') ? paymentType : '—',
        cashier:        _cashierName,
        ward:           (ward && ward !== 'All Wards') ? ward : '',
        remark:         remark,
        money_paid:     0,
        actual_amount:  actual,
        total_discount: discount,
        total_pay:      actual - discount,
        items: items.map(i => ({
            service_name: i.serviceName,
            quantity:     i.qty,
            unit_price:   i.unitPrice,
            discount_pct: i.discountPct,
            subtotal:     i.qty * i.unitPrice * (1 - i.discountPct / 100),
        })),
    });
}

/**
 * Print a specific saved invoice by its database ID.
 */
function printSingleInvoice(id) {
    const inv = invoicesData.find(i => i.id === id);
    if (!inv) { alert('Invoice data not found. Please reload the page.'); return; }
    openReceiptPopup(inv);
}

/**
 * Open a print-ready receipt in a new popup window.
 */
function openReceiptPopup(inv) {
    const w = window.open('', '_blank', 'width=820,height=740');
    w.document.write(buildReceiptHtml(inv));
    w.document.close();
    w.onload = () => { w.focus(); w.print(); };
}

/**
 * Build the full HTML string for a receipt.
 */
function buildReceiptHtml(inv) {
    const isDraft  = inv.invoice_number === 'DRAFT';
    const change   = Math.max(0, inv.money_paid - inv.total_pay);

    const rows = inv.items.map((item, i) => `
        <tr>
            <td class="tc">${i + 1}</td>
            <td>${escHtml(item.service_name)}</td>
            <td class="tc">${item.quantity}</td>
            <td class="tr">${fmt(item.unit_price)}</td>
            <td class="tc">${item.discount_pct > 0 ? item.discount_pct + '%' : '—'}</td>
            <td class="tr">${fmt(item.subtotal)}</td>
        </tr>`).join('');

    return `<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Invoice ${escHtml(inv.invoice_number)} — ${escHtml(_patientName)}</title>
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: 'Segoe UI', Arial, sans-serif; padding: 32px; font-size: 13px; color: #1e293b; position: relative; }
    ${isDraft ? `
    body::after {
        content: 'DRAFT';
        position: fixed; top: 50%; left: 50%;
        transform: translate(-50%,-50%) rotate(-28deg);
        font-size: 90px; font-weight: 900;
        color: rgba(0,0,0,.055); pointer-events: none; z-index: 0;
    }` : ''}
    h1   { text-align: center; font-size: 22px; color: #1a2e4a; margin-bottom: 3px; }
    .sub { text-align: center; font-size: 12px; color: #64748b; margin-bottom: 12px; letter-spacing: .5px; }
    .divider { border: none; border-top: 2px solid #1a2e4a; margin: 6px 0 18px; }
    .info-grid { display: flex; gap: 28px; flex-wrap: wrap; margin-bottom: 20px; }
    .info-block dt { font-size: 10px; text-transform: uppercase; letter-spacing: .4px; color: #94a3b8; margin-bottom: 2px; }
    .info-block dd { font-size: 13px; font-weight: 700; }
    table { width: 100%; border-collapse: collapse; font-size: 12px; }
    thead th { background: #1a2e4a; color: #fff; padding: 8px 10px; text-align: left; font-weight: 600; }
    tbody td { border-bottom: 1px solid #e2e8f0; padding: 7px 10px; }
    tbody tr:last-child td { border-bottom: none; }
    .tc { text-align: center; }
    .tr { text-align: right; }
    .totals-wrap { display: flex; justify-content: flex-end; margin-top: 16px; }
    .totals-table { width: 290px; border-collapse: collapse; }
    .totals-table td { padding: 5px 10px; font-size: 12px; }
    .totals-table .lbl { color: #64748b; }
    .totals-table .val { text-align: right; font-weight: 600; }
    .totals-table .grand td { font-size: 15px; font-weight: 700; border-top: 2px solid #1a2e4a; padding-top: 8px; }
    .totals-table .paid  td { color: #16a34a; }
    .totals-table .chg   td { color: #d97706; }
    .remark-box { margin-top: 16px; padding: 8px 12px; background: #f8fafc; border-left: 3px solid #e2e8f0; border-radius: 4px; font-size: 12px; color: #475569; }
    .footer { display: flex; justify-content: space-between; align-items: flex-end; margin-top: 48px; }
    .cashier-info { font-size: 12px; color: #475569; line-height: 1.7; }
    .sig-box { text-align: center; }
    .sig-line { border-top: 1px solid #1e293b; width: 170px; margin: 46px auto 6px; }
    .sig-lbl { font-size: 11px; color: #475569; }
    @media print { body { padding: 16px; } }
  </style>
</head>
<body>
  <h1>${escHtml(_appName)}</h1>
  <div class="sub">INVOICE${isDraft ? ' — DRAFT' : ''}</div>
  <hr class="divider">

  <dl class="info-grid">
    <div class="info-block"><dt>Invoice No.</dt><dd>${escHtml(inv.invoice_number)}</dd></div>
    <div class="info-block"><dt>Date</dt><dd>${escHtml(inv.invoice_date)}</dd></div>
    <div class="info-block"><dt>Patient</dt><dd>${escHtml(_patientName)}</dd></div>
    <div class="info-block"><dt>Patient ID</dt><dd>${escHtml(_patientId)}</dd></div>
    ${inv.ward ? `<div class="info-block"><dt>Ward</dt><dd>${escHtml(inv.ward)}</dd></div>` : ''}
    <div class="info-block"><dt>Payment Method</dt><dd>${escHtml(inv.payment_type)}</dd></div>
  </dl>

  <table>
    <thead>
      <tr>
        <th style="width:34px" class="tc">No.</th>
        <th>Service</th>
        <th style="width:48px" class="tc">Qty</th>
        <th style="width:92px" class="tr">Unit Price</th>
        <th style="width:66px" class="tc">Disc.</th>
        <th style="width:92px" class="tr">Amount</th>
      </tr>
    </thead>
    <tbody>${rows}</tbody>
  </table>

  <div class="totals-wrap">
    <table class="totals-table">
      <tr>
        <td class="lbl">Actual Amount</td>
        <td class="val">${fmt(inv.actual_amount)}</td>
      </tr>
      <tr>
        <td class="lbl">Discount</td>
        <td class="val" style="color:#dc2626;">- ${fmt(inv.total_discount)}</td>
      </tr>
      <tr class="grand">
        <td class="lbl">Total Pay</td>
        <td class="val">${fmt(inv.total_pay)}</td>
      </tr>
      ${inv.money_paid > 0 ? `
      <tr class="paid">
        <td class="lbl">Money Paid</td>
        <td class="val">${fmt(inv.money_paid)}</td>
      </tr>
      <tr class="chg">
        <td class="lbl">Change</td>
        <td class="val">${fmt(change)}</td>
      </tr>` : ''}
    </table>
  </div>

  ${inv.remark ? `<div class="remark-box"><strong>Note:</strong> ${escHtml(inv.remark)}</div>` : ''}

  <div class="footer">
    <div class="cashier-info">
      <div>Cashier: <strong>${escHtml(inv.cashier || '—')}</strong></div>
      <div style="margin-top:2px;color:#94a3b8;font-size:11px;">Thank you for your payment.</div>
    </div>
    <div class="sig-box">
      <div class="sig-line"></div>
      <div class="sig-lbl">Authorized Signature &amp; Stamp</div>
    </div>
  </div>
</body>
</html>`;
}

/* ════════════════════════════════════════════════
   Print All Invoices — full statement popup
════════════════════════════════════════════════ */
function printAllInvoices() {
    if (!invoicesData || invoicesData.length === 0) {
        alert('No invoices to print.');
        return;
    }

    let grandActual = 0, grandDiscount = 0, grandTotal = 0, grandPaid = 0;

    const invoiceBlocks = invoicesData.map((inv, index) => {
        grandActual   += inv.actual_amount;
        grandDiscount += inv.total_discount;
        grandTotal    += inv.total_pay;
        grandPaid     += inv.money_paid;

        const change = Math.max(0, inv.money_paid - inv.total_pay);

        const rows = inv.items.map((item, i) => `
            <tr>
                <td class="tc">${i + 1}</td>
                <td>${escHtml(item.service_name)}</td>
                <td class="tc">${item.quantity}</td>
                <td class="tr">${fmt(item.unit_price)}</td>
                <td class="tc">${item.discount_pct > 0 ? item.discount_pct + '%' : '—'}</td>
                <td class="tr">${fmt(item.subtotal)}</td>
            </tr>`).join('');

        const isLast = index === invoicesData.length - 1;

        return `
        <div class="inv-block${isLast ? '' : ' pb'}">
            <div class="inv-hdr">
                <div class="inv-hdr-top">
                    <span class="inv-num">${escHtml(inv.invoice_number)}</span>
                    <span class="inv-dt">${escHtml(inv.invoice_date)}</span>
                </div>
                <div class="inv-meta">
                    <span>Payment: <strong>${escHtml(inv.payment_type)}</strong></span>
                    <span>Cashier: <strong>${escHtml(inv.cashier || '—')}</strong></span>
                    ${inv.ward ? `<span>Ward: <strong>${escHtml(inv.ward)}</strong></span>` : ''}
                </div>
            </div>
            <table>
                <thead>
                    <tr>
                        <th style="width:34px" class="tc">No.</th>
                        <th>Service</th>
                        <th style="width:48px" class="tc">Qty</th>
                        <th style="width:92px" class="tr">Unit Price</th>
                        <th style="width:66px" class="tc">Disc.</th>
                        <th style="width:92px" class="tr">Amount</th>
                    </tr>
                </thead>
                <tbody>${rows}</tbody>
            </table>
            <div class="inv-totals">
                <span>Actual: <strong>${fmt(inv.actual_amount)}</strong></span>
                ${inv.total_discount > 0
                    ? `<span class="disc">Discount: <strong>- ${fmt(inv.total_discount)}</strong></span>`
                    : ''}
                <span class="ttl">Total Pay: <strong>${fmt(inv.total_pay)}</strong></span>
                ${inv.money_paid > 0
                    ? `<span class="paid">Paid: <strong>${fmt(inv.money_paid)}</strong></span>
                       <span class="chg">Change: <strong>${fmt(change)}</strong></span>`
                    : ''}
            </div>
            ${inv.remark
                ? `<div class="inv-remark">Note: ${escHtml(inv.remark)}</div>`
                : ''}
        </div>`;
    }).join('');

    const outstanding = grandTotal - grandPaid;
    const printDate   = new Date().toLocaleDateString('en-GB');

    const html = `<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Invoice Statement — ${escHtml(_patientName)}</title>
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: 'Segoe UI', Arial, sans-serif; padding: 32px; font-size: 13px; color: #1e293b; }
    h1   { text-align: center; font-size: 21px; color: #1a2e4a; margin-bottom: 3px; }
    .sub { text-align: center; font-size: 12px; color: #64748b; margin-bottom: 12px; letter-spacing: .5px; }
    .divider { border: none; border-top: 2px solid #1a2e4a; margin: 6px 0 18px; }

    /* Patient strip */
    .pt-strip { display: flex; gap: 28px; flex-wrap: wrap; padding: 10px 14px; background: #f8fafc;
                border: 1px solid #e2e8f0; border-radius: 6px; margin-bottom: 20px; }
    .pt-strip dt { font-size: 10px; text-transform: uppercase; color: #94a3b8; margin-bottom: 1px; }
    .pt-strip dd { font-size: 13px; font-weight: 700; }

    /* Invoice block */
    .inv-block { border: 1px solid #e2e8f0; border-radius: 6px; overflow: hidden; margin-bottom: 16px; }
    .inv-block.pb { page-break-after: avoid; }

    /* Invoice header */
    .inv-hdr { background: #f1f5f9; padding: 8px 12px; border-bottom: 1px solid #e2e8f0; }
    .inv-hdr-top { display: flex; justify-content: space-between; align-items: center; margin-bottom: 3px; }
    .inv-num { font-family: monospace; font-weight: 700; font-size: 13px; color: #1a2e4a; }
    .inv-dt  { font-size: 12px; color: #475569; }
    .inv-meta { display: flex; gap: 20px; font-size: 11px; color: #64748b; }
    .inv-meta strong { color: #1e293b; }

    /* Items table */
    table { width: 100%; border-collapse: collapse; font-size: 12px; }
    thead th { background: #1a2e4a; color: #fff; padding: 7px 10px; text-align: left; font-weight: 600; }
    tbody td { border-bottom: 1px solid #f1f5f9; padding: 6px 10px; }
    tbody tr:last-child td { border-bottom: none; }
    .tc { text-align: center; }
    .tr { text-align: right; }

    /* Per-invoice totals bar */
    .inv-totals { display: flex; gap: 18px; flex-wrap: wrap; padding: 7px 12px;
                  background: #f8fafc; border-top: 1px solid #e2e8f0; font-size: 12px; }
    .inv-totals .ttl  { font-weight: 700; }
    .inv-totals .ttl strong { color: #1a2e4a; }
    .inv-totals .disc strong { color: #dc2626; }
    .inv-totals .paid strong { color: #16a34a; }
    .inv-totals .chg  strong { color: #d97706; }
    .inv-remark { padding: 5px 12px 7px; font-size: 11px; color: #64748b; }

    /* Grand summary */
    .grand { border: 2px solid #1a2e4a; border-radius: 8px; overflow: hidden; margin-top: 24px; }
    .grand-hdr { background: #1a2e4a; color: #fff; padding: 10px 16px; font-weight: 700; font-size: 14px; }
    .grand-body { display: flex; gap: 32px; flex-wrap: wrap; padding: 14px 16px; background: #f8fafc; }
    .gs dt { font-size: 10px; text-transform: uppercase; color: #94a3b8; margin-bottom: 3px; }
    .gs dd { font-size: 17px; font-weight: 700; }
    .gs.ttl dd { color: #1a2e4a; }
    .gs.paid dd { color: #16a34a; }
    .gs.owed dd { color: ${outstanding > 0 ? '#dc2626' : '#16a34a'}; }

    /* Footer */
    .print-footer { margin-top: 32px; display: flex; justify-content: flex-end; }
    .sig-box { text-align: center; }
    .sig-line { border-top: 1px solid #1e293b; width: 170px; margin: 44px auto 6px; }
    .sig-lbl { font-size: 11px; color: #475569; }

    @media print { body { padding: 16px; } }
  </style>
</head>
<body>
  <h1>${escHtml(_appName)}</h1>
  <div class="sub">INVOICE STATEMENT</div>
  <hr class="divider">

  <dl class="pt-strip">
    <div><dt>Patient</dt><dd>${escHtml(_patientName)}</dd></div>
    <div><dt>Patient ID</dt><dd>${escHtml(_patientId)}</dd></div>
    <div><dt>Total Invoices</dt><dd>${invoicesData.length}</dd></div>
    <div><dt>Printed</dt><dd>${printDate}</dd></div>
  </dl>

  ${invoiceBlocks}

  <div class="grand">
    <div class="grand-hdr">Grand Summary</div>
    <div class="grand-body">
      <dl class="gs">
        <dt>Total Actual</dt>
        <dd>${fmt(grandActual)}</dd>
      </dl>
      <dl class="gs">
        <dt>Total Discount</dt>
        <dd style="color:#dc2626;">- ${fmt(grandDiscount)}</dd>
      </dl>
      <dl class="gs ttl">
        <dt>Total Pay</dt>
        <dd>${fmt(grandTotal)}</dd>
      </dl>
      <dl class="gs paid">
        <dt>Total Paid</dt>
        <dd>${fmt(grandPaid)}</dd>
      </dl>
      <dl class="gs owed">
        <dt>Outstanding</dt>
        <dd>${fmt(Math.max(0, outstanding))}</dd>
      </dl>
    </div>
  </div>

  <div class="print-footer">
    <div class="sig-box">
      <div class="sig-line"></div>
      <div class="sig-lbl">Authorized Signature &amp; Stamp</div>
    </div>
  </div>
</body>
</html>`;

    const w = window.open('', '_blank', 'width=920,height=760');
    w.document.write(html);
    w.document.close();
    w.onload = () => { w.focus(); w.print(); };
}

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
