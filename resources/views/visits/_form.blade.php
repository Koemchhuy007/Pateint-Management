<div class="row">
    <div class="col-md-6 mb-3">
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
    <div class="col-md-6 mb-3">
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

<div class="row">
    <div class="col-md-6 mb-3">
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

<div class="mb-3">
    <label class="form-label fw-semibold">Notes</label>
    <textarea name="notes"
              class="form-control @error('notes') is-invalid @enderror"
              rows="2">{{ old('notes', $visit->notes ?? '') }}</textarea>
    @error('notes')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
