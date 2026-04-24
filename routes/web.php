<?php

use App\Http\Controllers\FormationController;
use Illuminate\Support\Facades\Route;

Route::get('/', [FormationController::class, 'home'])->name('home');
Route::get('/formations', [FormationController::class, 'index'])->name('formations.index');
Route::get('/formations/create', [FormationController::class, 'create'])->name('formations.create');
Route::post('/formations', [FormationController::class, 'store'])->name('formations.store');
Route::get('/formations/{formation}/edit', [FormationController::class, 'edit'])->name('formations.edit');
Route::put('/formations/{formation}', [FormationController::class, 'update'])->name('formations.update');
Route::delete('/formations/{formation}', [FormationController::class, 'destroy'])->name('formations.destroy');
