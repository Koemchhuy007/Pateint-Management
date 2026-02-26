<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    // ──────────────────────────────────────────────────────────
    // Helpers
    // ──────────────────────────────────────────────────────────

    /**
     * Roles a system_admin is allowed to assign.
     * Only super_admin may create / manage system_admin accounts
     * (those are handled in Admin\SystemAdminController).
     */
    private function availableRoles(): array
    {
        if (auth()->user()->isSystemAdmin()) {
            return array_filter(
                User::ROLES,
                fn($key) => in_array($key, ['doctor', 'cashier'], true),
                ARRAY_FILTER_USE_KEY
            );
        }

        return User::ROLES;
    }

    /**
     * Abort 403 if the acting system_admin tries to touch a user that:
     *  - belongs to a different client, OR
     *  - is itself a system_admin account.
     *
     * Super admins are never blocked here.
     */
    private function guardAccess(User $target): void
    {
        $actor = auth()->user();

        if ($actor->isSuper()) {
            return;
        }

        if ($actor->isSystemAdmin()) {
            // Cross-tenant attempt
            if ((int) $actor->client_id !== (int) $target->client_id) {
                abort(403, 'You do not have permission to manage this user.');
            }
            // Attempting to touch another system_admin account
            if ($target->role === 'system_admin') {
                abort(403, 'System admins cannot manage other system admin accounts.');
            }
        }
    }

    // ──────────────────────────────────────────────────────────
    // CRUD
    // ──────────────────────────────────────────────────────────

    public function index()
    {
        $actor = auth()->user();

        $query = User::where('is_super', false)->orderBy('name');

        if ($actor->isSystemAdmin()) {
            // Only users belonging to this admin's client, excluding other system_admins
            $query->where('client_id', $actor->client_id)
                  ->where('role', '!=', 'system_admin');
        }

        $users = $query->get();

        return view('settings.users.index', compact('users'));
    }

    public function create()
    {
        $availableRoles = $this->availableRoles();

        return view('settings.users.create', compact('availableRoles'));
    }

    public function store(Request $request)
    {
        $actor        = auth()->user();
        $allowedRoles = array_keys($this->availableRoles());

        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:100'],
            'username' => ['required', 'string', 'max:50', 'unique:users', 'alpha_dash'],
            'email'    => ['required', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role'     => ['required', Rule::in($allowedRoles)],
        ]);

        // Bind new user to the system_admin's client automatically.
        // Super admins creating through Settings get no client (use Admin panel for that).
        $validated['client_id'] = $actor->isSystemAdmin() ? $actor->client_id : null;

        User::create($validated);

        return redirect()->route('settings.users.index')
            ->with('success', 'User "' . $validated['name'] . '" created successfully.');
    }

    public function edit(User $user)
    {
        $this->guardAccess($user);

        $availableRoles = $this->availableRoles();

        return view('settings.users.edit', compact('user', 'availableRoles'));
    }

    public function update(Request $request, User $user)
    {
        $this->guardAccess($user);

        $allowedRoles = array_keys($this->availableRoles());

        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:100'],
            'username' => ['required', 'string', 'max:50', 'alpha_dash', Rule::unique('users')->ignore($user)],
            'email'    => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'role'     => ['required', Rule::in($allowedRoles)],
        ]);

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

        $this->guardAccess($user);

        $user->delete();

        return redirect()->route('settings.users.index')
            ->with('success', 'User deleted.');
    }
}
