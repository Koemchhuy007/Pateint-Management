<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServiceGroup;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index(ServiceGroup $serviceGroup)
    {
        $services = $serviceGroup->services()->orderBy('name')->get();
        return view('settings.service-groups.services', compact('serviceGroup', 'services'));
    }

    public function store(Request $request, ServiceGroup $serviceGroup)
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:150'],
            'price'       => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string', 'max:255'],
        ]);

        $validated['service_group_id'] = $serviceGroup->id;
        $validated['is_active'] = true;

        Service::create($validated);

        return redirect()->route('settings.service-groups.services.index', $serviceGroup)
            ->with('success', 'Service "' . $validated['name'] . '" added.');
    }

    public function update(Request $request, ServiceGroup $serviceGroup, Service $service)
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:150'],
            'price'       => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string', 'max:255'],
            'is_active'   => ['boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');
        $service->update($validated);

        return redirect()->route('settings.service-groups.services.index', $serviceGroup)
            ->with('success', 'Service updated.');
    }

    public function destroy(ServiceGroup $serviceGroup, Service $service)
    {
        $service->delete();

        return redirect()->route('settings.service-groups.services.index', $serviceGroup)
            ->with('success', 'Service deleted.');
    }
}
