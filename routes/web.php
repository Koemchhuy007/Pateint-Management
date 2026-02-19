<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\VisitController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check() ? redirect()->route('patients.index') : redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('address/districts', [\App\Http\Controllers\AddressController::class, 'districts'])->name('address.districts');
    Route::get('address/communities', [\App\Http\Controllers\AddressController::class, 'communities'])->name('address.communities');
    Route::get('address/villages', [\App\Http\Controllers\AddressController::class, 'villages'])->name('address.villages');
    Route::resource('patients', PatientController::class);
    Route::post('patients/{patient}/case/open',    [PatientController::class, 'openCase'])->name('patients.case.open');
    Route::get('patients/{patient}/case',          [PatientController::class, 'showCase'])->name('patients.case.show');
    Route::post('patients/{patient}/case/discard', [PatientController::class, 'discardCase'])->name('patients.case.discard');
    Route::resource('patients.visits', VisitController::class)->except(['index']);
});
