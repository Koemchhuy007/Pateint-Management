<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ClientController extends Controller
{
    public function index()
    {
        $clients = Client::withCount('users')
            ->orderBy('name')
            ->paginate(20);

        return view('admin.clients.index', compact('clients'));
    }

    public function create()
    {
        return view('admin.clients.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:150',
            'email'     => 'nullable|email|max:150',
            'phone'     => 'nullable|string|max:30',
            'address'   => 'nullable|string|max:500',
            'is_active' => 'boolean',
        ]);

        $validated['slug']      = Str::slug($validated['name']) . '-' . Str::random(6);
        $validated['is_active'] = $request->boolean('is_active', true);

        Client::create($validated);

        return redirect()->route('admin.clients.index')
            ->with('success', "Client \"{$validated['name']}\" created successfully.");
    }

    public function edit(Client $client)
    {
        return view('admin.clients.edit', compact('client'));
    }

    public function update(Request $request, Client $client)
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:150',
            'email'     => 'nullable|email|max:150',
            'phone'     => 'nullable|string|max:30',
            'address'   => 'nullable|string|max:500',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        $client->update($validated);

        return redirect()->route('admin.clients.index')
            ->with('success', "Client \"{$client->name}\" updated successfully.");
    }

    public function destroy(Client $client)
    {
        $name = $client->name;
        $client->delete();

        return redirect()->route('admin.clients.index')
            ->with('success', "Client \"{$name}\" deleted.");
    }
}
