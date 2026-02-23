@push('styles')
<style>
/* ── Drug autocomplete dropdown ── */
.drug-ac-wrap { position: relative; }
.drug-dropdown {
    position: absolute;
    top: calc(100% + 2px);
    left: 0; right: 0;
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    box-shadow: 0 4px 16px rgba(0,0,0,.12);
    z-index: 1050;
    max-height: 220px;
    overflow-y: auto;
    display: none;
}
.drug-item {
    padding: 8px 12px;
    cursor: pointer;
    font-size: .83rem;
    border-bottom: 1px solid #f1f5f9;
}
.drug-item:last-child { border-bottom: none; }
.drug-item:hover { background: #f1f5f9; }
.drug-item-name { font-weight: 600; color: #1e293b; }
.drug-item-meta { font-size: .73rem; color: #94a3b8; margin-top: 1px; }
.drug-item-stock-low { color: #d97706; }
.drug-item-stock-zero { color: #dc2626; }
</style>
@endpush

<div class="row">
    <div class="col-md-4 mb-3">
        <label class="form-label fw-semibold">Visit Date & Time <span class="text-danger">*</span></label>
        <input type="datetime-local"
               name="visit_date"
               class="form-control @error('visit_date') is-invalid @enderror"
               value="{{ old('visit_date', isset($visit) ? $visit->visit_date->format('Y-m-d\TH:i') : '') }}"
               required>
        @error('visit_date')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-4 mb-3">
        <label class="form-label fw-semibold">Visit Type <span class="text-danger">*</span></label>
        <div class="d-flex gap-3 mt-1">
            <div class="form-check">
                <input class="form-check-input" type="radio" name="visit_type" id="visitOPD" value="OPD"
                       {{ old('visit_type', $visit->visit_type ?? 'OPD') === 'OPD' ? 'checked' : '' }} required>
                <label class="form-check-label fw-semibold" style="color:#0ea5e9;" for="visitOPD">OPD</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="visit_type" id="visitIPD" value="IPD"
                       {{ old('visit_type', $visit->visit_type ?? '') === 'IPD' ? 'checked' : '' }}>
                <label class="form-check-label fw-semibold" style="color:#8b5cf6;" for="visitIPD">IPD</label>
            </div>
        </div>
        @error('visit_type')
            <div class="text-danger mt-1" style="font-size:.82rem;">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-4 mb-3">
        <label class="form-label fw-semibold">Doctor Name <span class="text-danger">*</span></label>
        <input type="text"
               name="doctor_name"
               class="form-control @error('doctor_name') is-invalid @enderror"
               value="{{ old('doctor_name', $visit->doctor_name ?? '') }}"
               maxlength="100"
               required>
        @error('doctor_name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="mb-3">
    <label class="form-label fw-semibold">Reason / Chief Complaint <span class="text-danger">*</span></label>
    <input type="text"
           name="reason"
           class="form-control @error('reason') is-invalid @enderror"
           value="{{ old('reason', $visit->reason ?? '') }}"
           maxlength="500"
           required>
    @error('reason')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label class="form-label fw-semibold">Diagnosis</label>
    <textarea name="diagnosis"
              class="form-control @error('diagnosis') is-invalid @enderror"
              rows="3">{{ old('diagnosis', $visit->diagnosis ?? '') }}</textarea>
    @error('diagnosis')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label class="form-label fw-semibold">Treatment / Prescription</label>
    <textarea name="treatment"
              class="form-control @error('treatment') is-invalid @enderror"
              rows="3">{{ old('treatment', $visit->treatment ?? '') }}</textarea>
    @error('treatment')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

{{-- ══════════════════════════════════════════════
     DOCTOR'S PRESCRIPTION
══════════════════════════════════════════════ --}}
@php
    $existingRows = old('prescription', isset($visit) ? ($visit->prescription ?? []) : []);
    if (empty($existingRows)) {
        $existingRows = [['medication'=>'','method'=>'','morning'=>'','afternoon'=>'','evening'=>'','night'=>'','number_day'=>'','quantity'=>'','unit'=>'','remark'=>'']];
    }
@endphp

<div class="mt-4">
    <div class="fw-semibold mb-2" style="color:#16a34a;font-size:.95rem;">
        <i class="bi bi-capsule-pill me-1"></i>Doctor's Prescription
    </div>

    <div class="table-responsive">
        <table class="table table-bordered align-middle mb-2" id="prescriptionTable" style="font-size:.82rem;min-width:900px;">
            <thead style="background:#16a34a;color:#fff;">
                <tr>
                    <th style="width:36px;" class="text-center">No.</th>
                    <th>Medication Name</th>
                    <th style="width:120px;">Method</th>
                    <th style="width:68px;" class="text-center">Morning</th>
                    <th style="width:74px;" class="text-center">Afternoon</th>
                    <th style="width:62px;" class="text-center">Evening</th>
                    <th style="width:56px;" class="text-center">Night</th>
                    <th style="width:80px;" class="text-center">Number Day</th>
                    <th style="width:70px;" class="text-center">Quantity</th>
                    <th style="width:100px;">Unit</th>
                    <th style="width:140px;">Remark</th>
                    <th style="width:36px;" class="text-center">
                        <button type="button" class="btn btn-sm btn-primary rounded-circle p-0"
                                style="width:24px;height:24px;line-height:1;" onclick="addPrescriptionRow()">
                            <i class="bi bi-plus"></i>
                        </button>
                    </th>
                </tr>
            </thead>
            <tbody id="prescriptionBody">
                @foreach($existingRows as $ri => $row)
                <tr>
                    <td class="text-center text-muted row-num">{{ $ri + 1 }}</td>
                    <td>
                        <div class="drug-ac-wrap">
                            <input type="text" name="prescription[{{ $ri }}][medication]"
                                   class="form-control form-control-sm med-ac"
                                   autocomplete="off"
                                   value="{{ $row['medication'] ?? '' }}"
                                   placeholder="Type to search…">
                            <input type="hidden" name="prescription[{{ $ri }}][drug_id]"
                                   value="{{ $row['drug_id'] ?? '' }}">
                            <div class="drug-dropdown"></div>
                        </div>
                    </td>
                    <td>
                        <select name="prescription[{{ $ri }}][method]" class="form-select form-select-sm">
                            <option value=""></option>
                            @foreach(['Swallow','Injection','Topical','Inhale','Sublingual','Drops','Suppository'] as $m)
                                <option value="{{ $m }}" {{ ($row['method'] ?? '') == $m ? 'selected' : '' }}>{{ $m }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td><input type="number" name="prescription[{{ $ri }}][morning]"    class="form-control form-control-sm text-center" min="0" value="{{ $row['morning']    ?? '' }}"></td>
                    <td><input type="number" name="prescription[{{ $ri }}][afternoon]"  class="form-control form-control-sm text-center" min="0" value="{{ $row['afternoon']  ?? '' }}"></td>
                    <td><input type="number" name="prescription[{{ $ri }}][evening]"    class="form-control form-control-sm text-center" min="0" value="{{ $row['evening']    ?? '' }}"></td>
                    <td><input type="number" name="prescription[{{ $ri }}][night]"      class="form-control form-control-sm text-center" min="0" value="{{ $row['night']      ?? '' }}"></td>
                    <td><input type="number" name="prescription[{{ $ri }}][number_day]" class="form-control form-control-sm text-center" min="0" value="{{ $row['number_day'] ?? '' }}"></td>
                    <td><input type="number" name="prescription[{{ $ri }}][quantity]"   class="form-control form-control-sm text-center" min="0" value="{{ $row['quantity']   ?? '' }}"></td>
                    <td>
                        <select name="prescription[{{ $ri }}][unit]" class="form-select form-select-sm">
                            <option value=""></option>
                            @foreach(['Pill','Tablet','Capsule','ml','mg','Drop','Patch','Sachet','Vial','Tube'] as $u)
                                <option value="{{ $u }}" {{ ($row['unit'] ?? '') == $u ? 'selected' : '' }}>{{ $u }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td><input type="text" name="prescription[{{ $ri }}][remark]" class="form-control form-control-sm" value="{{ $row['remark'] ?? '' }}" placeholder="Remark"></td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-danger rounded-circle p-0"
                                style="width:24px;height:24px;line-height:1;" onclick="removePrescriptionRow(this)">
                            <i class="bi bi-dash"></i>
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Consulting checkbox + free-text --}}
    <div class="mt-2">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="consulting" id="consultingCheck" value="1"
                   {{ old('consulting', isset($visit) ? $visit->consulting : false) ? 'checked' : '' }}>
            <label class="form-check-label fw-semibold" for="consultingCheck">Consulting</label>
        </div>
        <div id="consultingNotesBox" class="mt-2" style="{{ old('consulting', isset($visit) ? $visit->consulting : false) ? '' : 'display:none;' }}">
            <textarea name="notes"
                      id="consultingNotes"
                      class="form-control @error('notes') is-invalid @enderror"
                      rows="3"
                      placeholder="Consulting notes...">{{ old('notes', $visit->notes ?? '') }}</textarea>
            @error('notes')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

{{-- Follow-up Date (below Doctor's Prescription) --}}
<div class="row mt-3">
    <div class="col-md-4 mb-3">
        <label class="form-label fw-semibold">Follow-up Date</label>
        <input type="date"
               name="follow_up_date"
               class="form-control @error('follow_up_date') is-invalid @enderror"
               value="{{ old('follow_up_date', isset($visit) && $visit->follow_up_date ? $visit->follow_up_date->format('Y-m-d') : '') }}">
        @error('follow_up_date')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

@push('scripts')
<script>
// ── Drug autocomplete ──
const _drugsApiUrl = '{{ route("api.drugs") }}';
let _acTimer = null;

document.getElementById('prescriptionBody').addEventListener('input', function (e) {
    if (!e.target.classList.contains('med-ac')) return;
    // Clear stored drug_id when user manually edits the text field
    const hiddenId = e.target.parentElement.querySelector('input[name*="[drug_id]"]');
    if (hiddenId) hiddenId.value = '';

    clearTimeout(_acTimer);
    const input = e.target;
    _acTimer = setTimeout(() => _fetchDrugs(input), 220);
});

document.getElementById('prescriptionBody').addEventListener('blur', function (e) {
    if (!e.target.classList.contains('med-ac')) return;
    setTimeout(() => _hideDrugDrop(e.target), 180);
}, true);

function _fetchDrugs(input) {
    const q = input.value.trim();
    const wrap = input.parentElement;
    const drop = wrap.querySelector('.drug-dropdown');
    if (q.length < 1) { drop.style.display = 'none'; return; }

    fetch(`${_drugsApiUrl}?q=${encodeURIComponent(q)}`)
        .then(r => r.json())
        .then(list => {
            if (!list.length) { drop.style.display = 'none'; return; }
            drop.innerHTML = list.map(d => {
                let stockHtml = '';
                if (d.stock === 0) {
                    stockHtml = `<span class="drug-item-stock-zero ms-1">· Out of stock</span>`;
                } else if (d.stock <= 10) {
                    stockHtml = `<span class="drug-item-stock-low ms-1">· Low stock: ${d.stock}</span>`;
                } else {
                    stockHtml = `<span class="ms-1" style="color:#64748b;">· In stock: ${d.stock}</span>`;
                }
                return `<div class="drug-item"
                              data-drug-id="${d.id}"
                              data-name="${d.name.replace(/"/g,'&quot;')}"
                              data-unit="${(d.unit||'').replace(/"/g,'&quot;')}">
                    <div class="drug-item-name">${d.name}</div>
                    <div class="drug-item-meta">${d.type || ''}${d.unit ? ' · '+d.unit : ''}${stockHtml}</div>
                </div>`;
            }).join('');
            drop.style.display = 'block';

            drop.querySelectorAll('.drug-item').forEach(item => {
                item.addEventListener('mousedown', function (e) {
                    e.preventDefault();
                    input.value = this.dataset.name;
                    // Store the drug_id in the hidden input
                    const hiddenId = wrap.querySelector('input[name*="[drug_id]"]');
                    if (hiddenId) hiddenId.value = this.dataset.drugId || '';
                    // Fill the unit select
                    const row = input.closest('tr');
                    const unitSel = row.querySelector('select[name*="[unit]"]');
                    if (unitSel && this.dataset.unit) unitSel.value = this.dataset.unit;
                    drop.style.display = 'none';
                });
            });
        })
        .catch(() => { drop.style.display = 'none'; });
}

function _hideDrugDrop(input) {
    const drop = input.parentElement?.querySelector('.drug-dropdown');
    if (drop) drop.style.display = 'none';
}

// ── Consulting toggle ──
document.getElementById('consultingCheck').addEventListener('change', function () {
    const box = document.getElementById('consultingNotesBox');
    if (this.checked) {
        box.style.display = '';
        document.getElementById('consultingNotes').focus();
    } else {
        box.style.display = 'none';
        document.getElementById('consultingNotes').value = '';
    }
});

// ── Prescription row helpers ──
function addPrescriptionRow() {
    const tbody = document.getElementById('prescriptionBody');
    const idx   = tbody.rows.length;
    const tr    = document.createElement('tr');
    tr.innerHTML = `
        <td class="text-center text-muted row-num">${idx + 1}</td>
        <td>
            <div class="drug-ac-wrap">
                <input type="text" name="prescription[${idx}][medication]"
                       class="form-control form-control-sm med-ac"
                       autocomplete="off" placeholder="Type to search…">
                <input type="hidden" name="prescription[${idx}][drug_id]" value="">
                <div class="drug-dropdown"></div>
            </div>
        </td>
        <td>
            <select name="prescription[${idx}][method]" class="form-select form-select-sm">
                <option value=""></option>
                <option>Swallow</option><option>Injection</option><option>Topical</option>
                <option>Inhale</option><option>Sublingual</option><option>Drops</option><option>Suppository</option>
            </select>
        </td>
        <td><input type="number" name="prescription[${idx}][morning]"    class="form-control form-control-sm text-center" min="0"></td>
        <td><input type="number" name="prescription[${idx}][afternoon]"  class="form-control form-control-sm text-center" min="0"></td>
        <td><input type="number" name="prescription[${idx}][evening]"    class="form-control form-control-sm text-center" min="0"></td>
        <td><input type="number" name="prescription[${idx}][night]"      class="form-control form-control-sm text-center" min="0"></td>
        <td><input type="number" name="prescription[${idx}][number_day]" class="form-control form-control-sm text-center" min="0"></td>
        <td><input type="number" name="prescription[${idx}][quantity]"   class="form-control form-control-sm text-center" min="0"></td>
        <td>
            <select name="prescription[${idx}][unit]" class="form-select form-select-sm">
                <option value=""></option>
                <option>Pill</option><option>Tablet</option><option>Capsule</option><option>ml</option>
                <option>mg</option><option>Drop</option><option>Patch</option><option>Sachet</option>
                <option>Vial</option><option>Tube</option>
            </select>
        </td>
        <td><input type="text" name="prescription[${idx}][remark]" class="form-control form-control-sm" placeholder="Remark"></td>
        <td class="text-center">
            <button type="button" class="btn btn-sm btn-danger rounded-circle p-0"
                    style="width:24px;height:24px;line-height:1;" onclick="removePrescriptionRow(this)">
                <i class="bi bi-dash"></i>
            </button>
        </td>`;
    tbody.appendChild(tr);
    renumberRows();
}

function removePrescriptionRow(btn) {
    const tbody = document.getElementById('prescriptionBody');
    if (tbody.rows.length <= 1) return;
    btn.closest('tr').remove();
    renumberRows();
}

function renumberRows() {
    document.querySelectorAll('#prescriptionBody .row-num').forEach((td, i) => {
        td.textContent = i + 1;
    });
}
</script>
@endpush
