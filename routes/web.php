<?php

use App\Livewire\Scanner;
use App\Livewire\Dashboard;
use App\Livewire\GateSetup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', GateSetup::class)->name('gate.setup');
Route::get('/scan', Scanner::class)->name('scanner');
Route::get('/dashboard', Dashboard::class)->name('dashboard');

Route::post('/logout', function() {
    session()->flush();
    return redirect('/');
})->name('logout');

Route::get('/reset-gate', function (Request $request) {
    // Hapus session terkait gate
    $request->session()->forget(['event_id', 'gate_number', 'crew_id']);

    // Redirect ke halaman setup gate
    return redirect()->route('gate.setup');
})->name('reset.gate');






