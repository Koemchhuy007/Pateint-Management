@extends('layouts.app')

@section('title', 'Edit Drug')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('drugstore.index') }}">Drugstore</a></li>
<li class="breadcrumb-item active">Edit Drug</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-7">

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-pencil me-2 text-primary"></i>Edit Drug</span>
                <a href="{{ route('drugstore.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left me-1"></i>Back
                </a>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('drugstore.update', $drug) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">

                        <div class="col-12">
                            <label class="form-label fw-semibold">
                                Drug Name <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="name"
                                   class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name', $drug->name) }}"
                                   required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">
                                Drug Type <span class="text-danger">*</span>
                            </label>
                            <select name="drug_type_id"
                                    class="form-select @error('drug_type_id') is-invalid @enderror" required>
                                <option value="">— Select Type —</option>
                                @foreach($drugTypes as $type)
                                    <option value="{{ $type->id }}"
                                        {{ old('drug_type_id', $drug->drug_type_id) == $type->id ? 'selected' : '' }}>
                                        {{ $type->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('drug_type_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">Unit</label>
                            <select name="unit" class="form-select @error('unit') is-invalid @enderror">
                                <option value="">— Select Unit —</option>
                                @foreach(['Pill','Tablet','Capsule','ml','mg','Drop','Patch','Sachet','Vial','Tube','Bottle','Ampoule'] as $u)
                                    <option value="{{ $u }}"
                                        {{ old('unit', $drug->unit) == $u ? 'selected' : '' }}>{{ $u }}</option>
                                @endforeach
                            </select>
                            @error('unit')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">
                                Stock Quantity <span class="text-danger">*</span>
                            </label>
                            <input type="number" name="stock_quantity"
                                   class="form-control @error('stock_quantity') is-invalid @enderror"
                                   value="{{ old('stock_quantity', $drug->stock_quantity) }}"
                                   min="0" required>
                            @error('stock_quantity')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Description</label>
                            <textarea name="description" class="form-control" rows="3">{{ old('description', $drug->description) }}</textarea>
                        </div>

                        <div class="col-12 d-flex gap-2 mt-2">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="bi bi-check-lg me-1"></i>Update Drug
                            </button>
                            <a href="{{ route('drugstore.index') }}" class="btn btn-outline-secondary">Cancel</a>
                        </div>

                    </div>
                </form>
            </div>
        </div>

        {{-- Danger zone --}}
        <div class="card mt-3 border-danger" style="border-color:#fecaca !important;">
            <div class="card-body d-flex justify-content-between align-items-center py-3">
                <div>
                    <div class="fw-semibold text-danger" style="font-size:.88rem;">Delete this drug</div>
                    <div class="text-muted" style="font-size:.78rem;">This action cannot be undone.</div>
                </div>
                <form action="{{ route('drugstore.destroy', $drug) }}" method="POST"
                      onsubmit="return confirm('Delete {{ addslashes($drug->name) }}?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger">
                        <i class="bi bi-trash me-1"></i>Delete
                    </button>
                </form>
            </div>
        </div>

    </div>
</div>
@endsection
