@push('styles')
<style>
    /* ── Photo panel ── */
    .photo-panel {
        position: sticky;
        top: 116px;
        max-width: 160px;
    }
    .photo-upload-box {
        position: relative;
        width: 100%;
        aspect-ratio: 3 / 4;
        border-radius: 12px;
        overflow: hidden;
        background: #f1f5f9;
        border: 2px dashed #cbd5e1;
        cursor: pointer;
        transition: border-color .2s, box-shadow .2s;
    }
    .photo-upload-box:hover {
        border-color: #2563eb;
        box-shadow: 0 0 0 4px rgba(37,99,235,.1);
    }
    .photo-upload-box img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
        border-radius: 10px;
    }
    .photo-placeholder {
        width: 100%;
        height: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 6px;
        color: #94a3b8;
        user-select: none;
    }
    .photo-placeholder i { font-size: 2.2rem; }
    .photo-placeholder span { font-size: .72rem; font-weight: 500; }
    /* Hover overlay */
    .photo-upload-box .photo-overlay {
        position: absolute;
        inset: 0;
        background: rgba(0,0,0,.42);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 4px;
        color: #fff;
        opacity: 0;
        transition: opacity .2s;
        border-radius: 10px;
    }
    .photo-upload-box:hover .photo-overlay { opacity: 1; }
    .photo-overlay i { font-size: 1.4rem; }
    .photo-overlay span { font-size: .72rem; font-weight: 600; letter-spacing: .3px; }

    /* ── Section headings ── */
    .form-section-title {
        font-size: .72rem;
        font-weight: 700;
        letter-spacing: 1px;
        text-transform: uppercase;
        color: #94a3b8;
        padding-bottom: 6px;
        border-bottom: 1px solid #e2e8f0;
        margin-bottom: 14px;
        margin-top: 20px;
    }
    .form-section-title:first-child { margin-top: 0; }
</style>
@endpush

<div class="row g-4">

    {{-- ════════════════════════════════
         LEFT — Photo Panel
    ════════════════════════════════ --}}
    <div class="col-md-3 col-lg-2">
        <div class="photo-panel">

            {{-- Big rectangle photo box --}}
            <div class="photo-upload-box" onclick="document.getElementById('photoInput').click()">

                {{-- Existing photo (edit mode) --}}
                @if(isset($patient) && $patient->photo_url)
                    <img id="photoPreview" src="{{ $patient->photo_url }}" alt="Patient photo">
                    <div class="photo-overlay">
                        <i class="bi bi-camera-fill"></i>
                        <span>Change Photo</span>
                    </div>
                @else
                    {{-- Placeholder --}}
                    <div class="photo-placeholder" id="photoPlaceholder">
                        <i class="bi bi-person-bounding-box"></i>
                        <span>Click to upload photo</span>
                    </div>
                    {{-- Preview (hidden until file chosen) --}}
                    <img id="photoPreview" src="" alt="" style="display:none; position:absolute; inset:0; width:100%; height:100%; object-fit:cover; border-radius:14px;">
                    <div class="photo-overlay">
                        <i class="bi bi-camera-fill"></i>
                        <span>Upload Photo</span>
                    </div>
                @endif

            </div>

            {{-- Hidden file input --}}
            <input type="file"
                   id="photoInput"
                   name="photo"
                   accept="image/jpeg,image/png,image/jpg,image/gif"
                   class="d-none @error('photo') is-invalid @enderror">

            {{-- File name + hint --}}
            <p id="photoFileName" class="text-muted mt-2 mb-0 text-center" style="font-size:.78rem;"></p>
            <p class="text-muted text-center mb-0" style="font-size:.75rem;">JPG, PNG, GIF &nbsp;·&nbsp; Max 2 MB</p>
            @error('photo')
                <div class="text-danger text-center mt-1" style="font-size:.82rem;">{{ $message }}</div>
            @enderror

            {{-- Status --}}
            <div class="mt-3">
                <label class="form-label fw-semibold mb-1">Status <span class="text-danger">*</span></label>
                <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                    <option value="active"   {{ old('status', $patient->status ?? 'active') == 'active'   ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ old('status', $patient->status ?? '')        == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="archived" {{ old('status', $patient->status ?? '')        == 'archived' ? 'selected' : '' }}>Archived</option>
                </select>
            </div>

        </div>{{-- /.photo-panel --}}
    </div>

    {{-- ════════════════════════════════
         RIGHT — Form Fields
    ════════════════════════════════ --}}
    <div class="col-md-9 col-lg-10">

        {{-- ── Personal Information ── --}}
        <div class="form-section-title">Personal Information</div>
        <div class="row">
            <div class="col-sm-6 mb-3">
                <label class="form-label">
                    Patient ID
                    <span class="badge bg-secondary ms-1" style="font-size:.6rem;vertical-align:middle;">Auto</span>
                </label>
                @if(isset($patient))
                    {{-- Edit: show existing ID, not submitted --}}
                    <input type="text" class="form-control bg-light text-muted"
                           value="{{ $patient->patient_id }}" disabled>
                @else
                    {{-- Create: submit the previewed ID; server uses it if still free --}}
                    <input type="hidden" name="patient_id" value="{{ $patientId ?? '' }}">
                    <input type="text" class="form-control bg-light"
                           value="{{ $patientId ?? '' }}" readonly>
                @endif
            </div>
            <div class="col-sm-3 mb-3">
                <label class="form-label">Given Name <span class="text-danger">*</span></label>
                <input type="text" name="given_name"
                       class="form-control @error('given_name') is-invalid @enderror"
                       value="{{ old('given_name', $patient->given_name ?? '') }}" required>
                @error('given_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-sm-3 mb-3">
                <label class="form-label">Surname <span class="text-danger">*</span></label>
                <input type="text" name="surname"
                       class="form-control @error('surname') is-invalid @enderror"
                       value="{{ old('surname', $patient->surname ?? '') }}" required>
                @error('surname')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="row">
            <div class="col-sm-3 mb-3">
                <label class="form-label">Sex <span class="text-danger">*</span></label>
                <select name="sex" class="form-select @error('sex') is-invalid @enderror" required>
                    <option value="">Select</option>
                    <option value="male"   {{ old('sex', $patient->sex ?? '') == 'male'   ? 'selected' : '' }}>Male</option>
                    <option value="female" {{ old('sex', $patient->sex ?? '') == 'female' ? 'selected' : '' }}>Female</option>
                    <option value="other"  {{ old('sex', $patient->sex ?? '') == 'other'  ? 'selected' : '' }}>Other</option>
                </select>
                @error('sex')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-sm-3 mb-3">
                <label class="form-label">Date of Birth <span class="text-danger">*</span></label>
                <input type="date" name="date_of_birth"
                       class="form-control @error('date_of_birth') is-invalid @enderror"
                       value="{{ old('date_of_birth', optional($patient->date_of_birth ?? null)->format('Y-m-d')) }}" required>
                @error('date_of_birth')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-sm-2 mb-3">
                <label class="form-label">Age</label>
                <input type="text" id="age_display" class="form-control bg-light"
                       value="{{ isset($patient) && $patient->date_of_birth ? $patient->age . ' yrs' : '—' }}" readonly>
            </div>
            <div class="col-sm-2 mb-3">
                <label class="form-label">Marital Status</label>
                <select name="personal_status" class="form-select @error('personal_status') is-invalid @enderror">
                    <option value="">—</option>
                    <option value="single"   {{ old('personal_status', $patient->personal_status ?? '') == 'single'   ? 'selected' : '' }}>Single</option>
                    <option value="married"  {{ old('personal_status', $patient->personal_status ?? '') == 'married'  ? 'selected' : '' }}>Married</option>
                    <option value="divorced" {{ old('personal_status', $patient->personal_status ?? '') == 'divorced' ? 'selected' : '' }}>Divorced</option>
                </select>
            </div>
            <div class="col-sm-2 mb-3">
                <label class="form-label">Blood Type</label>
                <select name="blood_type" class="form-select @error('blood_type') is-invalid @enderror">
                    <option value="">—</option>
                    @foreach(['A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-'] as $bt)
                        <option value="{{ $bt }}" {{ old('blood_type', $patient->blood_type ?? '') == $bt ? 'selected' : '' }}>{{ $bt }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- ── Contact ── --}}
        <div class="form-section-title">Contact</div>
        <div class="row">
            <div class="col-sm-6 mb-3">
                <label class="form-label">Phone</label>
                <input type="text" name="phone"
                       class="form-control @error('phone') is-invalid @enderror"
                       value="{{ old('phone', $patient->phone ?? '') }}">
            </div>
            <div class="col-sm-6 mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email"
                       class="form-control @error('email') is-invalid @enderror"
                       value="{{ old('email', $patient->email ?? '') }}">
            </div>
        </div>

        {{-- ── Address ── --}}
        <div class="form-section-title">Address</div>
        <div class="row">
            <div class="col-12 mb-3">
                <label class="form-label">Street / House No.</label>
                <textarea name="address" class="form-control @error('address') is-invalid @enderror"
                          rows="2" placeholder="Street, house number...">{{ old('address', $patient->address ?? '') }}</textarea>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-3 mb-3">
                <label class="form-label">Province</label>
                <select name="province_id" id="province_id" class="form-select @error('province_id') is-invalid @enderror">
                    <option value="">— Province —</option>
                    @foreach($provinces ?? [] as $p)
                        <option value="{{ $p->id }}" {{ old('province_id', $patient->province_id ?? '') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-3 mb-3">
                <label class="form-label">District</label>
                <select name="district_id" id="district_id" class="form-select @error('district_id') is-invalid @enderror">
                    <option value="">— District —</option>
                    @if(isset($patient) && $patient->district)
                        <option value="{{ $patient->district_id }}" selected>{{ $patient->district->name }}</option>
                    @endif
                </select>
            </div>
            <div class="col-sm-3 mb-3">
                <label class="form-label">Community</label>
                <select name="community_id" id="community_id" class="form-select @error('community_id') is-invalid @enderror">
                    <option value="">— Community —</option>
                    @if(isset($patient) && $patient->community)
                        <option value="{{ $patient->community_id }}" selected>{{ $patient->community->name }}</option>
                    @endif
                </select>
            </div>
            <div class="col-sm-3 mb-3">
                <label class="form-label">Village</label>
                <select name="village_id" id="village_id" class="form-select @error('village_id') is-invalid @enderror">
                    <option value="">— Village —</option>
                    @if(isset($patient) && $patient->village)
                        <option value="{{ $patient->village_id }}" selected>{{ $patient->village->name }}</option>
                    @endif
                </select>
            </div>
        </div>

        {{-- ── Emergency & Insurance ── --}}
        <div class="form-section-title">Emergency &amp; Insurance</div>
        <div class="row">
            <div class="col-sm-4 mb-3">
                <label class="form-label">Emergency Contact Name</label>
                <input type="text" name="emergency_contact_name"
                       class="form-control @error('emergency_contact_name') is-invalid @enderror"
                       value="{{ old('emergency_contact_name', $patient->emergency_contact_name ?? '') }}">
            </div>
            <div class="col-sm-4 mb-3">
                <label class="form-label">Emergency Contact Phone</label>
                <input type="text" name="emergency_contact_phone"
                       class="form-control @error('emergency_contact_phone') is-invalid @enderror"
                       value="{{ old('emergency_contact_phone', $patient->emergency_contact_phone ?? '') }}">
            </div>
            <div class="col-sm-4 mb-3">
                <label class="form-label">Insurance Info</label>
                <input type="text" name="insurance_info"
                       class="form-control @error('insurance_info') is-invalid @enderror"
                       value="{{ old('insurance_info', $patient->insurance_info ?? '') }}"
                       placeholder="Provider or policy number">
            </div>
        </div>

        {{-- ── Medical Notes ── --}}
        <div class="form-section-title">Medical Notes</div>
        <div class="mb-3">
            <textarea name="medical_notes"
                      class="form-control @error('medical_notes') is-invalid @enderror"
                      rows="3"
                      placeholder="Allergies, chronic conditions, etc.">{{ old('medical_notes', $patient->medical_notes ?? '') }}</textarea>
        </div>

    </div>{{-- /.col right --}}
</div>{{-- /.row --}}

@push('scripts')
<script>
// ── Photo preview ──
document.getElementById('photoInput').addEventListener('change', function () {
    const file = this.files[0];
    if (!file) return;
    document.getElementById('photoFileName').textContent = file.name;
    const reader = new FileReader();
    reader.onload = function (e) {
        const preview = document.getElementById('photoPreview');
        const placeholder = document.getElementById('photoPlaceholder');
        preview.src = e.target.result;
        preview.style.display = 'block';
        preview.style.position = 'absolute';
        preview.style.inset = '0';
        preview.style.borderRadius = '14px';
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
        clearOptions(villageSelect,   '— Village —');
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
        clearOptions(villageSelect,   '— Village —');
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
