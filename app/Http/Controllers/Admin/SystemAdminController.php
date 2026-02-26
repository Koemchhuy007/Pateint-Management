<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\User;
use Illuminate\Http\Request;

class SystemAdminController extends Controller
{
    /** List all system_admin users (non-super). */
    public function index()
    {
        $systemAdmins = User::where('role', 'system_admin')
            ->where('is_super', false)
            ->with('client')
            ->orderBy('name')
            ->paginate(25);

        return view('admin.system-admins.index', compact('systemAdmins'));
    }

    public function create()
    {
        $clients = Client::where('is_active', true)->orderBy('name')->get();

        return view('admin.system-admins.create', compact('clients'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'                  => 'required|string|max:150',
            'username'              => 'required|string|max:50|unique:users',
            'email'                 => 'nullable|email|max:150|unique:users',
            'password'              => 'required|string|min:8|confirmed',
            'client_id'             => 'nullable|exists:clients,id',
        ]);

        $user = User::create([
            'name'      => $validated['name'],
            'username'  => $validated['username'],
            'email'     => $validated['email'] ?? null,
            'password'  => $validated['password'],
            'role'      => 'system_admin',
            'is_super'  => false,
            'client_id' => $validated['client_id'] ?? null,
        ]);

        return redirect()->route('admin.system-admins.index')
            ->with('success', "System admin \"{$user->name}\" created successfully.");
    }

    public function destroy(User $user)
    {
        // Only allow deleting system_admin (non-super) accounts
        if ($user->is_super || $user->role !== 'system_admin') {
            abort(403, 'Cannot delete this account.');
        }

        $name = $user->name;
        $user->delete();

        return redirect()->route('admin.system-admins.index')
            ->with('success', "System admin \"{$name}\" deleted.");
    }
}
