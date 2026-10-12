<?php

use App\Http\Controllers\PlanoController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReservaController;

// La página de inicio lleva al login. Si ya has iniciado sesión,
// Breeze te redirige automáticamente a las reservas.
Route::get('/', fn () => redirect()->route('login'));

Route::get('/dashboard', function () {
        return redirect()->route('reservas.index');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    // CRUD de reservas. Sin destroy: las reservas se cancelan, no se borran
    Route::resource('reservas', ReservaController::class)->except(['destroy']);
    Route::patch('reservas/{reserva}/cancelar', [ReservaController::class, 'cancelar'])->name('reservas.cancelar');
    // Plano de la sala con el estado de cada mesa
    Route::get('plano', PlanoController::class)->name('plano');
});

require __DIR__.'/auth.php';
