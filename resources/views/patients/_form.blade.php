@push('styles')
<style>
/* ══ Photo upload ══ */
.photo-upload-wrap {
    position: relative;
    width: 160px;
    aspect-ratio: 3/4;
    border-radius: 12px;
    overflow: hidden;
    background: #f1f5f9;
    border: 2px dashed #cbd5e1;
    cursor: pointer;
    transition: border-color .2s, box-shadow .2s;
    margin: 0 auto;
}
.photo-upload-wrap:hover {
    border-color: #2563eb;
    box-shadow: 0 0 0 4px rgba(37,99,235,.1);
}
.photo-upload-wrap img {
    width: 100%; height: 100%; object-fit: cover; display: block;
}
.photo-placeholder {
    width: 100%; height: 100%;
    display: flex; flex-direction: column;
    align-items: center; justify-content: center;
    gap: 6px; color: #94a3b8; user-select: none;
}
.photo-placeholder i { font-size: 2.4rem; }
.photo-placeholder span { font-size: .68rem; font-weight: 500; text-align: center; line-height: 1.3; }
.photo-overlay {
    position: absolute; inset: 0;
    background: rgba(0,0,0,.45);
    display: flex; flex-direction: column;
    align-items: center; justify-content: center;
    gap: 4px; color: #fff;
    opacity: 0; transition: opacity .2s;
}
.photo-upload-wrap:hover .photo-overlay { opacity: 1; }
.photo-overlay i { font-size: 1.2rem; }
.photo-overlay span { font-size: .68rem; font-weight: 600; }

/* ══ Form section card ══ */
.form-section {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 20px 22px;
    margin-bottom: 16px;
}
.form-section-header {
    display: flex; align-items: center; gap: 8px;
    font-size: .72rem; font-weight: 700;
    letter-spacing: 1px; text-transform: uppercase;
    color: #64748b;
    padding-bottom: 14px;
    margin-bottom: 16px;
    border-bottom: 1px solid #f1f5f9;
}
.form-section-header .section-icon {
    width: 26px; height: 26px;
    background: #eff6ff; color: #2563eb;
    border-radius: 7px;
    display: inline-flex; align-items: center; justify-content: center;
    font-size: .85rem;
    flex-shrink: 0;
}
</style>
@endpush

{{-- ── Personal Information ── --}}
<div class="form-section">
    <div class="form-section-header">
        <span class="section-icon"><i class="bi bi-person-fill"></i></span>
        {{ __('patient.section_personal') }}
    </div>
    <div class="row g-3 align-items-start">

        {{-- Fields col --}}
        <div class="col-md-9">
            <div class="row g-3">
                <div class="col-sm-4">
                    <label class="form-label fw-semibold" style="font-size:.83rem;">
                        {{ __('field.patient_id') }}
                        <span class="badge bg-secondary ms-1" style="font-size:.58rem;vertical-align:middle;">Auto</span>
                    </label>
                    @if(isset($patient) && $patient->exists)
                        <input type="text" class="form-control bg-light text-muted"
                               value="{{ $patient->patient_id }}" disabled>
                    @else
                        <input type="hidden" name="patient_id" value="{{ $patientId ?? '' }}">
                        <input type="text" class="form-control bg-light"
                               value="{{ $patientId ?? '' }}" readonly style="color:#64748b;">
                    @endif
                </div>
                <div class="col-sm-4">
                    <label class="form-label fw-semibold" style="font-size:.83rem;">
                        Given Name <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="given_name"
                           class="form-control @error('given_name') is-invalid @enderror"
                           value="{{ old('given_name', $patient->given_name ?? '') }}" required>
                    @error('given_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-sm-4">
                    <label class="form-label fw-semibold" style="font-size:.83rem;">
                        Surname <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="surname"
                           class="form-control @error('surname') is-invalid @enderror"
                           value="{{ old('surname', $patient->surname ?? '') }}" required>
                    @error('surname')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-sm-3">
                    <label class="form-label fw-semibold" style="font-size:.83rem;">
                        {{ __('field.sex') }} <span class="text-danger">*</span>
                    </label>
                    <select name="sex" class="form-select @error('sex') is-invalid @enderror" required>
                        <option value="">{{ __('placeholder.select') }}</option>
                        <option value="male"   {{ old('sex', $patient->sex ?? '') == 'male'   ? 'selected' : '' }}>{{ __('gender.male') }}</option>
                        <option value="female" {{ old('sex', $patient->sex ?? '') == 'female' ? 'selected' : '' }}>{{ __('gender.female') }}</option>
                        <option value="other"  {{ old('sex', $patient->sex ?? '') == 'other'  ? 'selected' : '' }}>{{ __('gender.other') }}</option>
                    </select>
                    @error('sex')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-sm-4">
                    <label class="form-label fw-semibold" style="font-size:.83rem;">
                        {{ __('field.dob') }} <span class="text-danger">*</span>
                    </label>
                    <input type="date" name="date_of_birth"
                           class="form-control @error('date_of_birth') is-invalid @enderror"
                           value="{{ old('date_of_birth', optional($patient->date_of_birth ?? null)->format('Y-m-d')) }}" required>
                    @error('date_of_birth')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-sm-2">
                    <label class="form-label fw-semibold" style="font-size:.83rem;">{{ __('field.age') }}</label>
                    <input type="text" id="age_display" class="form-control bg-light text-muted"
                           value="{{ isset($patient) && $patient->date_of_birth ? $patient->age . ' yrs' : '—' }}" readonly>
                </div>
                <div class="col-sm-3">
                    <label class="form-label fw-semibold" style="font-size:.83rem;">{{ __('field.marital_status') }}</label>
                    <select name="personal_status" class="form-select">
                        <option value="">—</option>
                        <option value="single"   {{ old('personal_status', $patient->personal_status ?? '') == 'single'   ? 'selected' : '' }}>Single</option>
                        <option value="married"  {{ old('personal_status', $patient->personal_status ?? '') == 'married'  ? 'selected' : '' }}>Married</option>
                        <option value="divorced" {{ old('personal_status', $patient->personal_status ?? '') == 'divorced' ? 'selected' : '' }}>Divorced</option>
                    </select>
                </div>
                <div class="col-sm-3">
                    <label class="form-label fw-semibold" style="font-size:.83rem;">{{ __('field.blood_type') }}</label>
                    <select name="blood_type" class="form-select">
                        <option value="">—</option>
                        @foreach(['A+','A-','B+','B-','O+','O-','AB+','AB-'] as $bt)
                            <option value="{{ $bt }}" {{ old('blood_type', $patient->blood_type ?? '') == $bt ? 'selected' : '' }}>{{ $bt }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        {{-- Photo upload col ── right side --}}
        <div class="col-md-3 text-center">
            <label class="form-label fw-semibold d-block mb-2" style="font-size:.83rem;">Photo</label>
            <div class="photo-upload-wrap" onclick="document.getElementById('photoInput').click()">
                @if(isset($patient) && $patient->photo_url)
                    <img id="photoPreview" src="{{ $patient->photo_url }}" alt="Photo">
                @else
                    <div class="photo-placeholder" id="photoPlaceholder">
                        <i class="bi bi-person-bounding-box"></i>
                        <span>Click to upload</span>
                    </div>
                    <img id="photoPreview" src="" alt=""
                         style="display:none;position:absolute;inset:0;width:100%;height:100%;object-fit:cover;">
                @endif
                <div class="photo-overlay">
                    <i class="bi bi-camera-fill"></i>
                    <span>{{ isset($patient) && $patient->photo_url ? 'Change' : 'Upload' }}</span>
                </div>
            </div>
            <input type="file" id="photoInput" name="photo"
                   accept="image/jpeg,image/png,image/jpg,image/gif" class="d-none">
            <p class="text-muted mt-2 mb-0" style="font-size:.7rem;">JPG, PNG · Max 2 MB</p>
            @error('photo')
                <div class="text-danger mt-1" style="font-size:.78rem;">{{ $message }}</div>
            @enderror
        </div>

    </div>
</div>

{{-- ── Contact ── --}}
<div class="form-section">
    <div class="form-section-header">
        <span class="section-icon"><i class="bi bi-telephone-fill"></i></span>
        {{ __('patient.section_contact') }}
    </div>
    <div class="row g-3">
        <div class="col-sm-6">
            <label class="form-label fw-semibold" style="font-size:.83rem;">{{ __('field.phone') }}</label>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0 text-muted">
                    <i class="bi bi-phone"></i>
                </span>
                <input type="text" name="phone"
                       class="form-control border-start-0 @error('phone') is-invalid @enderror"
                       value="{{ old('phone', $patient->phone ?? '') }}"
                       placeholder="e.g. 012 345 678">
            </div>
        </div>
        <div class="col-sm-6">
            <label class="form-label fw-semibold" style="font-size:.83rem;">Email</label>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0 text-muted">
                    <i class="bi bi-envelope"></i>
                </span>
                <input type="email" name="email"
                       class="form-control border-start-0 @error('email') is-invalid @enderror"
                       value="{{ old('email', $patient->email ?? '') }}"
                       placeholder="email@example.com">
            </div>
        </div>
    </div>
</div>

{{-- ── Address ── --}}
<div class="form-section">
    <div class="form-section-header">
        <span class="section-icon"><i class="bi bi-geo-alt-fill"></i></span>
        {{ __('patient.section_address') }}
    </div>
    <div class="row g-3">
        <div class="col-12">
            <label class="form-label fw-semibold" style="font-size:.83rem;">Street / House No.</label>
            <textarea name="address" class="form-control" rows="2"
                      placeholder="Street, house number...">{{ old('address', $patient->address ?? '') }}</textarea>
        </div>
        <div class="col-sm-3">
            <label class="form-label fw-semibold" style="font-size:.83rem;">{{ __('field.province') }}</label>
            <select name="province_id" id="province_id" class="form-select">
                <option value="">{{ __('placeholder.province') }}</option>
                @foreach($provinces ?? [] as $p)
                    <option value="{{ $p->id }}" {{ old('province_id', $patient->province_id ?? '') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-sm-3">
            <label class="form-label fw-semibold" style="font-size:.83rem;">{{ __('field.district') }}</label>
            <select name="district_id" id="district_id" class="form-select">
                <option value="">{{ __('placeholder.district') }}</option>
                @if(isset($patient) && $patient->district)
                    <option value="{{ $patient->district_id }}" selected>{{ $patient->district->name }}</option>
                @endif
            </select>
        </div>
        <div class="col-sm-3">
            <label class="form-label fw-semibold" style="font-size:.83rem;">{{ __('field.community') }}</label>
            <select name="community_id" id="community_id" class="form-select">
                <option value="">{{ __('placeholder.community') }}</option>
                @if(isset($patient) && $patient->community)
                    <option value="{{ $patient->community_id }}" selected>{{ $patient->community->name }}</option>
                @endif
            </select>
        </div>
        <div class="col-sm-3">
            <label class="form-label fw-semibold" style="font-size:.83rem;">{{ __('field.village') }}</label>
            <select name="village_id" id="village_id" class="form-select">
                <option value="">{{ __('placeholder.village') }}</option>
                @if(isset($patient) && $patient->village)
                    <option value="{{ $patient->village_id }}" selected>{{ $patient->village->name }}</option>
                @endif
            </select>
        </div>
    </div>
</div>

{{-- ── Emergency & Insurance ── --}}
<div class="form-section">
    <div class="form-section-header">
        <span class="section-icon" style="background:#fff7ed;color:#ea580c;"><i class="bi bi-exclamation-triangle-fill"></i></span>
        {{ __('patient.section_emergency') }}
    </div>
    <div class="row g-3">
        <div class="col-sm-4">
            <label class="form-label fw-semibold" style="font-size:.83rem;">{{ __('field.emergency_contact') }}</label>
            <input type="text" name="emergency_contact_name"
                   class="form-control @error('emergency_contact_name') is-invalid @enderror"
                   value="{{ old('emergency_contact_name', $patient->emergency_contact_name ?? '') }}"
                   placeholder="{{ __('placeholder.full_name') }}">
        </div>
        <div class="col-sm-4">
            <label class="form-label fw-semibold" style="font-size:.83rem;">{{ __('field.phone') }}</label>
            <input type="text" name="emergency_contact_phone"
                   class="form-control @error('emergency_contact_phone') is-invalid @enderror"
                   value="{{ old('emergency_contact_phone', $patient->emergency_contact_phone ?? '') }}"
                   placeholder="{{ __('placeholder.phone') }}">
        </div>
        <div class="col-sm-4">
            <label class="form-label fw-semibold" style="font-size:.83rem;">Insurance Info</label>
            <input type="text" name="insurance_info"
                   class="form-control @error('insurance_info') is-invalid @enderror"
                   value="{{ old('insurance_info', $patient->insurance_info ?? '') }}"
                   placeholder="Provider or policy number">
        </div>
    </div>
</div>

{{-- ── Medical Notes ── --}}
<div class="form-section mb-0">
    <div class="form-section-header">
        <span class="section-icon" style="background:#f0fdf4;color:#16a34a;"><i class="bi bi-journal-medical"></i></span>
        {{ __('patient.section_medical') }}
    </div>
    <textarea name="medical_notes" class="form-control" rows="3"
              placeholder="Allergies, chronic conditions, important notes...">{{ old('medical_notes', $patient->medical_notes ?? '') }}</textarea>
</div>

@push('scripts')
<script>
// ── Photo preview ──
document.getElementById('photoInput').addEventListener('change', function () {
    const file = this.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = function (e) {
        const preview     = document.getElementById('photoPreview');
        const placeholder = document.getElementById('photoPlaceholder');
        preview.src = e.target.result;
        preview.style.display = 'block';
        preview.style.position = 'absolute';
        preview.style.inset = '0';
        if (placeholder) placeholder.style.display = 'none';
    };
    reader.readAsDataURL(file);
});

// ── Age calculator ──
document.addEventListener('DOMContentLoaded', function () {
    const dobInput   = document.querySelector('input[name="date_of_birth"]');
    const ageDisplay = document.getElementById('age_display');
    if (dobInput && ageDisplay) {
        function updateAge() {
            const val = dobInput.value;
            if (!val) { ageDisplay.value = '—'; return; }
            const dob   = new Date(val);
            const today = new Date();
            let age = today.getFullYear() - dob.getFullYear();
            const m = today.getMonth() - dob.getMonth();
            if (m < 0 || (m === 0 && today.getDate() < dob.getDate())) age--;
            ageDisplay.value = age >= 0 ? age + ' yrs' : '—';
        }
        dobInput.addEventListener('change', updateAge);
    }

    // ── Cascading address dropdowns ──
    const provinceSelect  = document.getElementById('province_id');
    const districtSelect  = document.getElementById('district_id');
    const communitySelect = document.getElementById('community_id');
    const villageSelect   = document.getElementById('village_id');
    const baseUrl = '{{ url("/") }}';

    function clearOptions(select, placeholder) {
        select.innerHTML = `<option value="">${placeholder}</option>`;
    }

    const initialDistrictId  = districtSelect?.value;
    const initialCommunityId = communitySelect?.value;
    const initialVillageId   = villageSelect?.value;

    function loadDistricts(selectId) {
        const provinceId = provinceSelect.value;
        clearOptions(districtSelect, '— District —');
        clearOptions(communitySelect, '— Community —');
        clearOptions(villageSelect, '— Village —');
        if (!provinceId) return Promise.resolve();
        return fetch(`${baseUrl}/address/districts?province_id=${provinceId}`)
            .then(r => r.json())
            .then(data => {
                data.forEach(d => districtSelect.add(new Option(d.name, d.id)));
                if (selectId) districtSelect.value = selectId;
            });
    }

    function loadCommunities(selectId) {
        const districtId = districtSelect.value;
        clearOptions(communitySelect, '— Community —');
        clearOptions(villageSelect, '— Village —');
        if (!districtId) return Promise.resolve();
        return fetch(`${baseUrl}/address/communities?district_id=${districtId}`)
            .then(r => r.json())
            .then(data => {
                data.forEach(d => communitySelect.add(new Option(d.name, d.id)));
                if (selectId) communitySelect.value = selectId;
            });
    }

    function loadVillages(selectId) {
        const communityId = communitySelect.value;
        clearOptions(villageSelect, '— Village —');
        if (!communityId) return Promise.resolve();
        return fetch(`${baseUrl}/address/villages?community_id=${communityId}`)
            .then(r => r.json())
            .then(data => {
                data.forEach(d => villageSelect.add(new Option(d.name, d.id)));
                if (selectId) villageSelect.value = selectId;
            });
    }

    provinceSelect?.addEventListener('change', () => loadDistricts());
    districtSelect?.addEventListener('change', () => loadCommunities());
    communitySelect?.addEventListener('change', () => loadVillages());

    if (provinceSelect?.value) {
        loadDistricts(initialDistrictId).then(() => {
            if (initialCommunityId) loadCommunities(initialCommunityId).then(() => {
                if (initialVillageId) loadVillages(initialVillageId);
            });
        });
    }
});
</script>
@endpush
