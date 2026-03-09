<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('registros-diarios', 'records.index')
    ->middleware(['auth'])
    ->name('records.index');

Route::view('colaboradores', 'colaboradores.index')
    ->middleware(['auth', 'supervisor'])
    ->name('colaboradores.index');

Route::view('skus', 'skus.index')
    ->middleware(['auth', 'supervisor'])
    ->name('skus.index');

Route::view('bono', 'bono.index')
    ->middleware(['auth', 'supervisor'])
    ->name('bono.index');

Route::view('productividad', 'productividad.index')
    ->middleware(['auth', 'supervisor'])
    ->name('productividad.index');

Route::view('asistencias', 'asistencias.index')
    ->middleware(['auth'])
    ->name('asistencias.index');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__.'/auth.php';
