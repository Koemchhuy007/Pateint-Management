<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\Patient;
use App\Models\User;

class AdminController extends Controller
{
    /** Super admin dashboard with platform-wide stats. */
    public function index()
    {
        $stats = [
            'clients'  => Client::count(),
            'users'    => User::where('is_super', false)->count(),
            'patients' => Patient::withoutGlobalScopes()->count(),
            'invoices' => Invoice::withoutGlobalScopes()->count(),
        ];

        $clients = Client::withCount('users')
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        return view('admin.index', compact('stats', 'clients'));
    }
}
