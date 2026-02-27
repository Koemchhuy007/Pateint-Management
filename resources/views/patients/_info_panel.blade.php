@push('styles')
<style>
/* ── Sticky panel ── */
.info-panel { position: sticky; top: 116px; }

/* ── Profile header ── */
.patient-profile-photo {
    width: 90px;
    height: 108px;
    border-radius: 12px;
    overflow: hidden;
    background: #e2e8f0;
    border: 3px solid #fff;
    box-shadow: 0 2px 10px rgba(0,0,0,.12);
    flex-shrink: 0;
}
.patient-profile-photo img {
    width: 100%; height: 100%; object-fit: cover;
}
.patient-profile-photo .photo-placeholder {
    width: 100%; height: 100%;
    display: flex; align-items: center; justify-content: center;
    color: #94a3b8;
}

/* ── Info rows ── */
.info-row {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    padding: 7px 0;
    border-bottom: 1px solid #f1f5f9;
}
.info-row:last-child { border-bottom: none; }
.info-row-icon {
    width: 28px; height: 28px;
    border-radius: 7px;
    background: #f1f5f9;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
    color: #64748b;
    font-size: .8rem;
}
.info-row-label {
    font-size: .72rem;
    font-weight: 600;
    color: #94a3b8;
    text-transform: uppercase;
    letter-spacing: .4px;
    line-height: 1;
    margin-bottom: 2px;
}
.info-row-value {
    font-size: .85rem;
    color: #1e293b;
    line-height: 1.3;
    word-break: break-word;
}

/* ── Section title ── */
.panel-section-title {
    font-size: .68rem;
    font-weight: 700;
    letter-spacing: 1px;
    text-transform: uppercase;
    color: #94a3b8;
    display: flex;
    align-items: center;
    gap: 6px;
    margin: 16px 0 8px;
}
.panel-section-title::after {
    content: '';
    flex: 1;
    height: 1px;
    background: #e2e8f0;
}
</style>
@endpush

<div class="col-md-3">
    <div class="info-panel">
        <div class="card">
            <div class="card-body p-3">

                {{-- ── Profile Header ── --}}
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="patient-profile-photo">
                        @if($patient->photo_url)
                            <img src="{{ $patient->photo_url }}" alt="{{ $patient->full_name }}">
                        @else
                            <div class="photo-placeholder">
                                <i class="bi bi-person-bounding-box" style="font-size:2.2rem;"></i>
                            </div>
                        @endif
                    </div>
                    <div style="min-width:0;">
                        <div class="fw-bold" style="font-size:.95rem;line-height:1.3;color:#1e293b;">
                            {{ $patient->full_name }}
                        </div>
                        <div class="text-muted mt-1" style="font-size:.75rem;">
                            <i class="bi bi-hash" style="font-size:.7rem;"></i>{{ $patient->patient_id }}
                        </div>
                        <div class="mt-2 d-flex gap-1 flex-wrap">
                            @if($patient->blood_type)
                            <span class="badge" style="background:#fee2e2;color:#dc2626;font-size:.68rem;">
                                <i class="bi bi-droplet-fill me-1"></i>{{ $patient->blood_type }}
                            </span>
                            @endif
                            @if($patient->sex)
                            <span class="badge" style="background:#eff6ff;color:#2563eb;font-size:.68rem;">
                                {{ ucfirst($patient->sex) }}
                            </span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="d-grid mb-3">
                    <a href="{{ route('patients.edit', $patient) }}" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-pencil me-1"></i>{{ __('patient.edit') }}
                    </a>
                </div>

                {{-- ── Personal ── --}}
                <div class="panel-section-title"><i class="bi bi-person-fill"></i>{{ __('patient.section_personal') }}</div>

                <div class="info-row">
                    <div class="info-row-icon"><i class="bi bi-calendar3"></i></div>
                    <div>
                        <div class="info-row-label">{{ __('field.dob') }}</div>
                        <div class="info-row-value">{{ $patient->date_of_birth->format('d/m/Y') }}</div>
                    </div>
                </div>

                <div class="info-row">
                    <div class="info-row-icon"><i class="bi bi-clock"></i></div>
                    <div>
                        <div class="info-row-label">{{ __('field.age') }}</div>
                        <div class="info-row-value">{{ $patient->age ? $patient->age . ' ' . __('patient.age_years') : '—' }}</div>
                    </div>
                </div>

                <div class="info-row">
                    <div class="info-row-icon"><i class="bi bi-heart"></i></div>
                    <div>
                        <div class="info-row-label">{{ __('field.marital_status') }}</div>
                        <div class="info-row-value">{{ ucfirst($patient->personal_status ?? '—') }}</div>
                    </div>
                </div>

                {{-- ── Contact ── --}}
                <div class="panel-section-title"><i class="bi bi-telephone-fill"></i>{{ __('patient.section_contact') }}</div>

                <div class="info-row">
                    <div class="info-row-icon"><i class="bi bi-phone"></i></div>
                    <div>
                        <div class="info-row-label">{{ __('field.phone') }}</div>
                        <div class="info-row-value">{{ $patient->phone ?? '—' }}</div>
                    </div>
                </div>

                <div class="info-row">
                    <div class="info-row-icon"><i class="bi bi-envelope"></i></div>
                    <div>
                        <div class="info-row-label">Email</div>
                        <div class="info-row-value">{{ $patient->email ?? '—' }}</div>
                    </div>
                </div>

                <div class="info-row">
                    <div class="info-row-icon"><i class="bi bi-geo-alt"></i></div>
                    <div>
                        <div class="info-row-label">{{ __('field.address') }}</div>
                        <div class="info-row-value">{{ $patient->full_address ?: ($patient->address ?? '—') }}</div>
                    </div>
                </div>

                {{-- ── Emergency ── --}}
                <div class="panel-section-title"><i class="bi bi-exclamation-triangle-fill"></i>{{ __('patient.section_emergency') }}</div>

                <div class="info-row">
                    <div class="info-row-icon"><i class="bi bi-person-lines-fill"></i></div>
                    <div>
                        <div class="info-row-label">{{ __('field.emergency_contact') }}</div>
                        <div class="info-row-value">{{ $patient->emergency_contact_name ?? '—' }}</div>
                    </div>
                </div>

                <div class="info-row">
                    <div class="info-row-icon"><i class="bi bi-telephone-fill"></i></div>
                    <div>
                        <div class="info-row-label">{{ __('field.phone') }}</div>
                        <div class="info-row-value">{{ $patient->emergency_contact_phone ?? '—' }}</div>
                    </div>
                </div>

                {{-- ── Insurance ── --}}
                @if($patient->insurance_info)
                <div class="panel-section-title"><i class="bi bi-shield-fill-check"></i>Insurance</div>
                <div class="info-row">
                    <div class="info-row-icon"><i class="bi bi-card-text"></i></div>
                    <div>
                        <div class="info-row-label">Policy / Provider</div>
                        <div class="info-row-value">{{ $patient->insurance_info }}</div>
                    </div>
                </div>
                @endif

                {{-- ── Medical Notes ── --}}
                @if($patient->medical_notes)
                <div class="panel-section-title"><i class="bi bi-journal-medical"></i>{{ __('patient.section_medical') }}</div>
                <div style="font-size:.83rem;color:#475569;line-height:1.55;background:#f8fafc;border-radius:8px;padding:10px 12px;">
                    {{ $patient->medical_notes }}
                </div>
                @endif

            </div>
        </div>
    </div>
</div>
