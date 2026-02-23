<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\ServiceGroup;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ServiceGroupController extends Controller
{
    public function index()
    {
        $serviceGroups = ServiceGroup::withCount('services')->orderBy('name')->get();
        return view('settings.service-groups.index', compact('serviceGroups'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:100', 'unique:service_groups'],
            'description' => ['nullable', 'string', 'max:255'],
        ]);

        ServiceGroup::create($validated);

        return redirect()->route('settings.service-groups.index')
            ->with('success', 'Service group "' . $validated['name'] . '" added.');
    }

    public function update(Request $request, ServiceGroup $serviceGroup)
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:100', Rule::unique('service_groups')->ignore($serviceGroup)],
            'description' => ['nullable', 'string', 'max:255'],
        ]);

        $serviceGroup->update($validated);

        return redirect()->route('settings.service-groups.index')
            ->with('success', 'Service group updated.');
    }

    public function destroy(ServiceGroup $serviceGroup)
    {
        if ($serviceGroup->services()->exists()) {
            return redirect()->route('settings.service-groups.index')
                ->with('error', 'Cannot delete "' . $serviceGroup->name . '" — it has services assigned.');
        }

        $serviceGroup->delete();

        return redirect()->route('settings.service-groups.index')
            ->with('success', 'Service group deleted.');
    }
}
