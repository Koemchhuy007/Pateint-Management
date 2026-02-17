<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label">Patient ID <span class="text-danger">*</span></label>
        <input type="text" name="patient_id" class="form-control @error('patient_id') is-invalid @enderror" value="{{ old('patient_id', $patient->patient_id ?? '') }}" placeholder="e.g. PAT-00123" required>
    </div>
    <div class="col-md-3 mb-3">
        <label class="form-label">Surname <span class="text-danger">*</span></label>
        <input type="text" name="surname" class="form-control @error('surname') is-invalid @enderror" value="{{ old('surname', $patient->surname ?? '') }}" required>
    </div>
    <div class="col-md-3 mb-3">
        <label class="form-label">Given Name <span class="text-danger">*</span></label>
        <input type="text" name="given_name" class="form-control @error('given_name') is-invalid @enderror" value="{{ old('given_name', $patient->given_name ?? '') }}" required>
    </div>
</div>

<div class="row">
    <div class="col-md-3 mb-3">
        <label class="form-label">Sex <span class="text-danger">*</span></label>
        <select name="sex" class="form-select @error('sex') is-invalid @enderror" required>
            <option value="">Select</option>
            <option value="male" {{ old('sex', $patient->sex ?? '') == 'male' ? 'selected' : '' }}>Male</option>
            <option value="female" {{ old('sex', $patient->sex ?? '') == 'female' ? 'selected' : '' }}>Female</option>
            <option value="other" {{ old('sex', $patient->sex ?? '') == 'other' ? 'selected' : '' }}>Other</option>
        </select>
    </div>
    <div class="col-md-3 mb-3">
        <label class="form-label">Date of Birth <span class="text-danger">*</span></label>
        <input type="date" name="date_of_birth" class="form-control @error('date_of_birth') is-invalid @enderror" value="{{ old('date_of_birth', optional($patient->date_of_birth)->format('Y-m-d') ?? '') }}" required>
    </div>
    <div class="col-md-2 mb-3">
        <label class="form-label">Age</label>
        <input type="text" id="age_display" class="form-control bg-light" value="{{ optional($patient)->date_of_birth ? $patient->age . ' years' : '—' }}" readonly>
    </div>
    <div class="col-md-2 mb-3">
        <label class="form-label">Personal Status</label>
        <select name="personal_status" class="form-select @error('personal_status') is-invalid @enderror">
            <option value="">—</option>
            <option value="single" {{ old('personal_status', $patient->personal_status ?? '') == 'single' ? 'selected' : '' }}>Single</option>
            <option value="married" {{ old('personal_status', $patient->personal_status ?? '') == 'married' ? 'selected' : '' }}>Married</option>
            <option value="divorced" {{ old('personal_status', $patient->personal_status ?? '') == 'divorced' ? 'selected' : '' }}>Divorced</option>
        </select>
    </div>
    <div class="col-md-2 mb-3">
        <label class="form-label">Blood Type</label>
        <select name="blood_type" class="form-select @error('blood_type') is-invalid @enderror">
            <option value="">—</option>
            @foreach(['A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-'] as $type)
                <option value="{{ $type }}" {{ old('blood_type', $patient->blood_type ?? '') == $type ? 'selected' : '' }}>{{ $type }}</option>
            @endforeach
        </select>
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $patient->email ?? '') }}">
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label">Phone</label>
        <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $patient->phone ?? '') }}">
    </div>
</div>

<h6 class="mt-4 mb-3">Address</h6>
<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label">Street / House No.</label>
        <textarea name="address" class="form-control @error('address') is-invalid @enderror" rows="2" placeholder="Street, house number...">{{ old('address', $patient->address ?? '') }}</textarea>
    </div>
</div>
<div class="row">
    <div class="col-md-3 mb-3">
        <label class="form-label">Province</label>
        <select name="province_id" id="province_id" class="form-select @error('province_id') is-invalid @enderror">
            <option value="">— Select Province —</option>
            @foreach($provinces ?? [] as $p)
                <option value="{{ $p->id }}" {{ old('province_id', $patient->province_id ?? '') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3 mb-3">
        <label class="form-label">District</label>
        <select name="district_id" id="district_id" class="form-select @error('district_id') is-invalid @enderror">
            <option value="">— Select District —</option>
            @if(isset($patient) && $patient->district)
                <option value="{{ $patient->district_id }}" selected>{{ $patient->district->name }}</option>
            @endif
        </select>
    </div>
    <div class="col-md-3 mb-3">
        <label class="form-label">Community</label>
        <select name="community_id" id="community_id" class="form-select @error('community_id') is-invalid @enderror">
            <option value="">— Select Community —</option>
            @if(isset($patient) && $patient->community)
                <option value="{{ $patient->community_id }}" selected>{{ $patient->community->name }}</option>
            @endif
        </select>
    </div>
    <div class="col-md-3 mb-3">
        <label class="form-label">Village</label>
        <select name="village_id" id="village_id" class="form-select @error('village_id') is-invalid @enderror">
            <option value="">— Select Village —</option>
            @if(isset($patient) && $patient->village)
                <option value="{{ $patient->village_id }}" selected>{{ $patient->village->name }}</option>
            @endif
        </select>
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label">Emergency Contact Name</label>
        <input type="text" name="emergency_contact_name" class="form-control @error('emergency_contact_name') is-invalid @enderror" value="{{ old('emergency_contact_name', $patient->emergency_contact_name ?? '') }}">
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label">Emergency Contact Phone</label>
        <input type="text" name="emergency_contact_phone" class="form-control @error('emergency_contact_phone') is-invalid @enderror" value="{{ old('emergency_contact_phone', $patient->emergency_contact_phone ?? '') }}">
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label">Insurance Info</label>
        <input type="text" name="insurance_info" class="form-control @error('insurance_info') is-invalid @enderror" value="{{ old('insurance_info', $patient->insurance_info ?? '') }}" placeholder="Insurance provider or policy number">
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label">Status <span class="text-danger">*</span></label>
        <select name="status" class="form-select @error('status') is-invalid @enderror" required>
            <option value="active" {{ old('status', $patient->status ?? 'active') == 'active' ? 'selected' : '' }}>Active</option>
            <option value="inactive" {{ old('status', $patient->status ?? '') == 'inactive' ? 'selected' : '' }}>Inactive</option>
            <option value="archived" {{ old('status', $patient->status ?? '') == 'archived' ? 'selected' : '' }}>Archived</option>
        </select>
    </div>
</div>

<div class="mb-3">
    <label class="form-label">Medical Notes</label>
    <textarea name="medical_notes" class="form-control @error('medical_notes') is-invalid @enderror" rows="3" placeholder="Allergies, chronic conditions, etc.">{{ old('medical_notes', $patient->medical_notes ?? '') }}</textarea>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const dobInput = document.querySelector('input[name="date_of_birth"]');
    const ageDisplay = document.getElementById('age_display');
    if (dobInput && ageDisplay) {
        function updateAge() {
            const val = dobInput.value;
            if (!val) { ageDisplay.value = '—'; return; }
            const dob = new Date(val);
            const today = new Date();
            let age = today.getFullYear() - dob.getFullYear();
            const m = today.getMonth() - dob.getMonth();
            if (m < 0 || (m === 0 && today.getDate() < dob.getDate())) age--;
            ageDisplay.value = age >= 0 ? age + ' years' : '—';
        }
        dobInput.addEventListener('change', updateAge);
    }

    const provinceSelect = document.getElementById('province_id');
    const districtSelect = document.getElementById('district_id');
    const communitySelect = document.getElementById('community_id');
    const villageSelect = document.getElementById('village_id');
    const baseUrl = '{{ url("/") }}';

    function clearOptions(select, placeholder) {
        select.innerHTML = `<option value="">${placeholder}</option>`;
    }

    const initialDistrictId = districtSelect?.value;
    const initialCommunityId = communitySelect?.value;
    const initialVillageId = villageSelect?.value;

    function loadDistricts(selectId) {
        const provinceId = provinceSelect.value;
        clearOptions(districtSelect, '— Select District —');
        clearOptions(communitySelect, '— Select Community —');
        clearOptions(villageSelect, '— Select Village —');
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
        clearOptions(communitySelect, '— Select Community —');
        clearOptions(villageSelect, '— Select Village —');
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
        clearOptions(villageSelect, '— Select Village —');
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
