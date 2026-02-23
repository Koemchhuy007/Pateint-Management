<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\PaymentType;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PaymentTypeController extends Controller
{
    public function index()
    {
        $paymentTypes = PaymentType::orderBy('name')->get();
        return view('settings.payment-types.index', compact('paymentTypes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:100', 'unique:payment_types'],
            'description' => ['nullable', 'string', 'max:255'],
        ]);

        PaymentType::create($validated);

        return redirect()->route('settings.payment-types.index')
            ->with('success', 'Payment type "' . $validated['name'] . '" added.');
    }

    public function update(Request $request, PaymentType $paymentType)
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:100', Rule::unique('payment_types')->ignore($paymentType)],
            'description' => ['nullable', 'string', 'max:255'],
            'is_active'   => ['boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');
        $paymentType->update($validated);

        return redirect()->route('settings.payment-types.index')
            ->with('success', 'Payment type updated.');
    }

    public function destroy(PaymentType $paymentType)
    {
        $paymentType->delete();

        return redirect()->route('settings.payment-types.index')
            ->with('success', 'Payment type deleted.');
    }
}
