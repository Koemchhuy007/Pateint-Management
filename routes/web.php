<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AdminSettingController;
use App\Http\Controllers\Admin\AdminTranslationController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\SystemAdminController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DrugController;
use App\Http\Controllers\DrugTypeController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\VisitController;
use App\Http\Controllers\Settings\PaymentTypeController;
use App\Http\Controllers\Settings\RolePermissionController;
use App\Http\Controllers\Settings\ServiceGroupController;
use App\Http\Controllers\Settings\ServiceController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\Settings\UserController as SettingUserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (!auth()->check()) {
        return redirect()->route('login');
    }

    return redirect()->route(auth()->user()->homeRoute());
});

// ── Language switch (no auth required — works on login page too) ──
Route::get('locale/{locale}', [LocaleController::class, 'switch'])->name('locale.switch');

Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [LoginController::class, 'logout'])->name('logout');

    // Address API (no feature restriction — used across features)
    Route::get('address/districts',  [\App\Http\Controllers\AddressController::class, 'districts'])->name('address.districts');
    Route::get('address/communities',[\App\Http\Controllers\AddressController::class, 'communities'])->name('address.communities');
    Route::get('address/villages',   [\App\Http\Controllers\AddressController::class, 'villages'])->name('address.villages');

    // Drug search API (used in visit prescription form — accessible to those with patients access)
    Route::get('api/drugs', [DrugController::class, 'apiSearch'])->name('api.drugs');

    // ── Patients ──────────────────────────────────────────────
    Route::middleware('role.permission:patients')->group(function () {
        Route::resource('patients', PatientController::class);
        Route::post('patients/{patient}/case/open',    [PatientController::class, 'openCase'])->name('patients.case.open');
        Route::get('patients/{patient}/case',          [PatientController::class, 'showCase'])->name('patients.case.show');
        Route::post('patients/{patient}/case/discard', [PatientController::class, 'discardCase'])->name('patients.case.discard');
        Route::resource('patients.visits', VisitController::class)->except(['index']);
        Route::post('patients/{patient}/visits/{visit}/discharge', [VisitController::class, 'discharge'])->name('patients.visits.discharge');
    });

    // ── Invoice ───────────────────────────────────────────────
    Route::middleware('role.permission:invoice')->group(function () {
        Route::get('invoices',              [InvoiceController::class, 'index'])->name('invoices.index');
        Route::get('invoices/{patient}',    [InvoiceController::class, 'show'])->name('invoices.show');
        Route::post('invoices/{patient}',   [InvoiceController::class, 'store'])->name('invoices.store');
        Route::delete('invoices/{invoice}', [InvoiceController::class, 'destroy'])->name('invoices.destroy');
    });

    // ── Drugstore ─────────────────────────────────────────────
    Route::middleware('role.permission:drugstore')->group(function () {
        Route::resource('drugstore', DrugController::class)
            ->parameters(['drugstore' => 'drug'])
            ->except(['show']);
        Route::resource('drug-types', DrugTypeController::class)
            ->except(['show', 'create', 'edit']);
    });

    // ── Reports ───────────────────────────────────────────────
    Route::middleware('role.permission:reports')->prefix('reports')->name('reports.')->group(function () {
        Route::get('/',               [ReportController::class, 'index'])->name('index');
        Route::get('patient-visits',  [ReportController::class, 'patientVisits'])->name('patient-visits');
        Route::get('drug-usage',      [ReportController::class, 'drugUsage'])->name('drug-usage');
        Route::get('drug-store',      [ReportController::class, 'drugStore'])->name('drug-store');
        Route::get('financial',       [ReportController::class, 'financial'])->name('financial');
    });

    // ── Settings (system_admin + client_users) ─────────────────
    Route::middleware('role.permission:settings')->prefix('settings')->name('settings.')->group(function () {

        Route::get('/', fn () => redirect()->route('settings.users.index'))->name('index');

        // Role Permissions
        Route::get('role-permissions',  [RolePermissionController::class, 'index'])->name('role-permissions.index');
        Route::put('role-permissions',  [RolePermissionController::class, 'update'])->name('role-permissions.update');

        // Payment Types
        Route::get('payment-types',                      [PaymentTypeController::class, 'index'])->name('payment-types.index');
        Route::post('payment-types',                     [PaymentTypeController::class, 'store'])->name('payment-types.store');
        Route::put('payment-types/{paymentType}',        [PaymentTypeController::class, 'update'])->name('payment-types.update');
        Route::delete('payment-types/{paymentType}',     [PaymentTypeController::class, 'destroy'])->name('payment-types.destroy');

        // Service Groups
        Route::get('service-groups',                     [ServiceGroupController::class, 'index'])->name('service-groups.index');
        Route::post('service-groups',                    [ServiceGroupController::class, 'store'])->name('service-groups.store');
        Route::put('service-groups/{serviceGroup}',      [ServiceGroupController::class, 'update'])->name('service-groups.update');
        Route::delete('service-groups/{serviceGroup}',   [ServiceGroupController::class, 'destroy'])->name('service-groups.destroy');

        // Services within a group
        Route::get('service-groups/{serviceGroup}/services',                          [ServiceController::class, 'index'])->name('service-groups.services.index');
        Route::post('service-groups/{serviceGroup}/services',                         [ServiceController::class, 'store'])->name('service-groups.services.store');
        Route::put('service-groups/{serviceGroup}/services/{service}',                [ServiceController::class, 'update'])->name('service-groups.services.update');
        Route::delete('service-groups/{serviceGroup}/services/{service}',             [ServiceController::class, 'destroy'])->name('service-groups.services.destroy');

        // Users
        Route::get('users',               [SettingUserController::class, 'index'])->name('users.index');
        Route::get('users/create',        [SettingUserController::class, 'create'])->name('users.create');
        Route::post('users',              [SettingUserController::class, 'store'])->name('users.store');
        Route::get('users/{user}/edit',   [SettingUserController::class, 'edit'])->name('users.edit');
        Route::put('users/{user}',        [SettingUserController::class, 'update'])->name('users.update');
        Route::delete('users/{user}',     [SettingUserController::class, 'destroy'])->name('users.destroy');
    });

    // ── Analytics (system_admin + super_admin) ────────────────
    Route::middleware('system_or_super.admin')->group(function () {
        Route::get('analytics', [AnalyticsController::class, 'index'])->name('analytics.index');
    });

    // ── Super Admin ───────────────────────────────────────────
    Route::middleware('super.admin')->prefix('admin')->name('admin.')->group(function () {

        Route::get('/', [AdminController::class, 'index'])->name('index');

        // Clients (tenants)
        Route::get('clients',                    [ClientController::class, 'index'])->name('clients.index');
        Route::get('clients/create',             [ClientController::class, 'create'])->name('clients.create');
        Route::post('clients',                   [ClientController::class, 'store'])->name('clients.store');
        Route::get('clients/{client}/edit',      [ClientController::class, 'edit'])->name('clients.edit');
        Route::put('clients/{client}',           [ClientController::class, 'update'])->name('clients.update');
        Route::delete('clients/{client}',        [ClientController::class, 'destroy'])->name('clients.destroy');

        // System admins (created only by super_admin)
        Route::get('system-admins',              [SystemAdminController::class, 'index'])->name('system-admins.index');
        Route::get('system-admins/create',       [SystemAdminController::class, 'create'])->name('system-admins.create');
        Route::post('system-admins',             [SystemAdminController::class, 'store'])->name('system-admins.store');
        Route::delete('system-admins/{user}',    [SystemAdminController::class, 'destroy'])->name('system-admins.destroy');

        // Users (all tenants)
        Route::get('users',                      [AdminUserController::class, 'index'])->name('users.index');
        Route::post('users/{user}/impersonate',  [AdminUserController::class, 'impersonate'])->name('users.impersonate');

        // Admin Settings (super_admin only)
        Route::prefix('settings')->name('settings.')->group(function () {
            Route::get('/',         [AdminSettingController::class,     'index'])->name('index');
            Route::put('/',         [AdminSettingController::class,     'update'])->name('update');
            Route::get('translations',  [AdminTranslationController::class, 'index'])->name('translations.index');
            Route::put('translations',  [AdminTranslationController::class, 'update'])->name('translations.update');
        });
    });

    // Stop impersonation (accessible without super.admin middleware, uses original id from session)
    Route::post('admin/stop-impersonate', [AdminUserController::class, 'stopImpersonate'])
        ->name('admin.stop-impersonate');
});
