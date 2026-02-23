<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $users = User::orderBy('name')->get();
        return view('settings.users.index', compact('users'));
    }

    public function create()
    {
        return view('settings.users.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:100'],
            'username' => ['required', 'string', 'max:50', 'unique:users', 'alpha_dash'],
            'email'    => ['required', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role'     => ['required', Rule::in(array_keys(User::ROLES))],
        ]);

        User::create($validated);

        return redirect()->route('settings.users.index')
            ->with('success', 'User "' . $validated['name'] . '" created successfully.');
    }

    public function edit(User $user)
    {
        return view('settings.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:100'],
            'username' => ['required', 'string', 'max:50', 'alpha_dash', Rule::unique('users')->ignore($user)],
            'email'    => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'role'     => ['required', Rule::in(array_keys(User::ROLES))],
        ]);

        // Only update password if a new one was provided
        if (empty($validated['password'])) {
            unset($validated['password']);
        }

        $user->update($validated);

        return redirect()->route('settings.users.index')
            ->with('success', 'User "' . $user->name . '" updated successfully.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('settings.users.index')
                ->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        return redirect()->route('settings.users.index')
            ->with('success', 'User deleted.');
    }
}
