<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminUserController extends Controller
{
    /** List all non-super users across every tenant. */
    public function index(Request $request)
    {
        $query = User::where('is_super', false)
            ->with('client')
            ->orderBy('name');

        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('username', 'like', "%{$s}%")
                  ->orWhere('email', 'like', "%{$s}%");
            });
        }

        $users   = $query->paginate(25)->withQueryString();
        $clients = Client::orderBy('name')->get();

        return view('admin.users.index', compact('users', 'clients'));
    }

    /** Impersonate a tenant user (stores original super admin id in session). */
    public function impersonate(User $user)
    {
        if ($user->is_super) {
            abort(403, 'Cannot impersonate another super admin.');
        }

        session(['impersonate_original_id' => auth()->id()]);
        auth()->login($user);

        return redirect()->route('patients.index')
            ->with('success', "You are now acting as {$user->name}.");
    }

    /** Stop impersonation and return to the super admin account. */
    public function stopImpersonate()
    {
        $originalId = session()->pull('impersonate_original_id');

        if (!$originalId) {
            return redirect()->route('patients.index');
        }

        $superAdmin = User::findOrFail($originalId);
        auth()->login($superAdmin);

        return redirect()->route('admin.index')
            ->with('success', 'Returned to your super admin account.');
    }
}
