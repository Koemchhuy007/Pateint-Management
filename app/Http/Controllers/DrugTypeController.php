<?php

namespace App\Http\Controllers;

use App\Models\DrugType;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DrugTypeController extends Controller
{
    public function index()
    {
        $types = DrugType::withCount('drugs')->orderBy('name')->get();
        return view('drug-types.index', compact('types'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:100', 'unique:drug_types'],
            'description' => ['nullable', 'string', 'max:255'],
        ]);

        DrugType::create($validated);

        return redirect()->route('drug-types.index')
            ->with('success', 'Drug type "' . $validated['name'] . '" added.');
    }

    public function update(Request $request, DrugType $drugType)
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:100', Rule::unique('drug_types')->ignore($drugType)],
            'description' => ['nullable', 'string', 'max:255'],
        ]);

        $drugType->update($validated);

        return redirect()->route('drug-types.index')
            ->with('success', 'Drug type updated.');
    }

    public function destroy(DrugType $drugType)
    {
        if ($drugType->drugs()->exists()) {
            return redirect()->route('drug-types.index')
                ->with('error', 'Cannot delete "' . $drugType->name . '" — it still has drugs assigned to it.');
        }

        $drugType->delete();

        return redirect()->route('drug-types.index')
            ->with('success', 'Drug type deleted.');
    }
}
