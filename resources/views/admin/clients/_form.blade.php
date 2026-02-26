{{-- Shared form fields for create/edit client --}}

<div class="mb-3">
    <label class="form-label fw-semibold">Client / Clinic Name <span class="text-danger">*</span></label>
    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
           value="{{ old('name', $client->name ?? '') }}" placeholder="e.g. Green Valley Clinic" required>
    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label fw-semibold">Email</label>
        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
               value="{{ old('email', $client->email ?? '') }}" placeholder="contact@clinic.com">
        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <label class="form-label fw-semibold">Phone</label>
        <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror"
               value="{{ old('phone', $client->phone ?? '') }}" placeholder="+855 23 000 000">
        @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>

<div class="mb-3 mt-3">
    <label class="form-label fw-semibold">Address</label>
    <textarea name="address" class="form-control @error('address') is-invalid @enderror"
              rows="2" placeholder="Street, City, Country">{{ old('address', $client->address ?? '') }}</textarea>
    @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<div class="form-check form-switch mt-3">
    <input class="form-check-input" type="checkbox" name="is_active" value="1" id="isActive"
           {{ old('is_active', ($client->is_active ?? true) ? '1' : '0') == '1' ? 'checked' : '' }}>
    <label class="form-check-label" for="isActive">Active (client can log in)</label>
</div>
