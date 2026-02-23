<?php

namespace App\Http\Controllers;

use App\Models\Drug;
use App\Models\DrugType;
use Illuminate\Http\Request;

class DrugController extends Controller
{
    public function index(Request $request)
    {
        $drugs = Drug::with('drugType')
            ->when($request->search, fn ($q) => $q->where('name', 'like', '%' . $request->search . '%'))
            ->when($request->type_id, fn ($q) => $q->where('drug_type_id', $request->type_id))
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        $drugTypes = DrugType::orderBy('name')->get();

        return view('drugstore.index', compact('drugs', 'drugTypes'));
    }

    public function create()
    {
        $drugTypes = DrugType::orderBy('name')->get();
        return view('drugstore.create', compact('drugTypes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'drug_type_id'   => ['required', 'exists:drug_types,id'],
            'name'           => ['required', 'string', 'max:150'],
            'unit'           => ['nullable', 'string', 'max:50'],
            'stock_quantity' => ['required', 'integer', 'min:0'],
            'description'    => ['nullable', 'string'],
        ]);

        Drug::create($validated);

        return redirect()->route('drugstore.index')
            ->with('success', 'Drug "' . $validated['name'] . '" added successfully.');
    }

    public function edit(Drug $drug)
    {
        $drugTypes = DrugType::orderBy('name')->get();
        return view('drugstore.edit', compact('drug', 'drugTypes'));
    }

    public function update(Request $request, Drug $drug)
    {
        $validated = $request->validate([
            'drug_type_id'   => ['required', 'exists:drug_types,id'],
            'name'           => ['required', 'string', 'max:150'],
            'unit'           => ['nullable', 'string', 'max:50'],
            'stock_quantity' => ['required', 'integer', 'min:0'],
            'description'    => ['nullable', 'string'],
        ]);

        $drug->update($validated);

        return redirect()->route('drugstore.index')
            ->with('success', 'Drug "' . $drug->name . '" updated successfully.');
    }

    public function destroy(Drug $drug)
    {
        $name = $drug->name;
        $drug->delete();

        return redirect()->route('drugstore.index')
            ->with('success', 'Drug "' . $name . '" deleted.');
    }

    /**
     * API endpoint: returns JSON list of drugs for autocomplete.
     * GET /api/drugs?q=xxx&type_id=xxx
     */
    public function apiSearch(Request $request)
    {
        $drugs = Drug::with('drugType')
            ->when($request->q, fn ($q) => $q->where('name', 'like', '%' . $request->q . '%'))
            ->when($request->type_id, fn ($q) => $q->where('drug_type_id', $request->type_id))
            ->orderBy('name')
            ->limit(15)
            ->get()
            ->map(fn ($d) => [
                'id'    => $d->id,
                'name'  => $d->name,
                'unit'  => $d->unit,
                'type'  => $d->drugType?->name,
                'stock' => $d->stock_quantity,
            ]);

        return response()->json($drugs);
    }
}
