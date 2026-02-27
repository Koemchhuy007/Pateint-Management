@extends('layouts.app')

@section('title', 'Add Drug')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('drugstore.index') }}">Drugstore</a></li>
<li class="breadcrumb-item active">Add Drug</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-7">

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-plus-circle me-2 text-success"></i>{{ __('drug.add_new') }}</span>
                <a href="{{ route('drugstore.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left me-1"></i>{{ __('common.back') }}
                </a>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('drugstore.store') }}" method="POST">
                    @csrf

                    <div class="row g-3">

                        <div class="col-12">
                            <label class="form-label fw-semibold">
                                {{ __('drug.name') }} <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="name"
                                   class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name') }}"
                                   placeholder="{{ __('placeholder.drug_name') }}"
                                   autofocus required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">
                                {{ __('drug.type') }} <span class="text-danger">*</span>
                            </label>
                            @if($drugTypes->isEmpty())
                                <div class="alert alert-warning py-2 mb-0" style="font-size:.84rem;">
                                    No drug types yet.
                                    <a href="{{ route('drug-types.index') }}">Add a type first →</a>
                                </div>
                                <input type="hidden" name="drug_type_id" value="">
                            @else
                                <select name="drug_type_id"
                                        class="form-select @error('drug_type_id') is-invalid @enderror" required>
                                    <option value="">{{ __('placeholder.select_type') }}</option>
                                    @foreach($drugTypes as $type)
                                        <option value="{{ $type->id }}"
                                            {{ old('drug_type_id') == $type->id ? 'selected' : '' }}>
                                            {{ $type->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('drug_type_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            @endif
                        </div>

                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">{{ __('drug.unit') }}</label>
                            <select name="unit" class="form-select @error('unit') is-invalid @enderror">
                                <option value="">{{ __('placeholder.select_unit') }}</option>
                                @foreach(['Pill','Tablet','Capsule','ml','mg','Drop','Patch','Sachet','Vial','Tube','Bottle','Ampoule'] as $u)
                                    <option value="{{ $u }}" {{ old('unit') == $u ? 'selected' : '' }}>{{ $u }}</option>
                                @endforeach
                            </select>
                            @error('unit')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">
                                {{ __('drug.stock_qty') }} <span class="text-danger">*</span>
                            </label>
                            <input type="number" name="stock_quantity"
                                   class="form-control @error('stock_quantity') is-invalid @enderror"
                                   value="{{ old('stock_quantity', 0) }}"
                                   min="0" required>
                            @error('stock_quantity')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Description</label>
                            <textarea name="description" class="form-control" rows="3"
                                      placeholder="Optional notes about dosage, storage, etc.">{{ old('description') }}</textarea>
                        </div>

                        <div class="col-12 d-flex gap-2 mt-2">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="bi bi-check-lg me-1"></i>{{ __('drug.save') }}
                            </button>
                            <a href="{{ route('drugstore.index') }}" class="btn btn-outline-secondary">{{ __('common.cancel') }}</a>
                        </div>

                    </div>
                </form>
            </div>
        </div>

    </div>
</div>
@endsection
