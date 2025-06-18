<?php

use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\InvitesController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TripController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::redirect('/', '/trips')->name('home');

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/trips', [TripController::class, 'index'])->name('trips.index');
    Route::get('/trips/invites', [InvitesController::class, 'index'])->name('trips.invites');
    Route::get('/trips/create', [TripController::class, 'create'])->name('trips.create');
    Route::get('/trips/{trip}', [TripController::class, 'show'])->name('trips.show');
    Route::post('/trips', [TripController::class, 'store'])->name('trips.store');
    Route::put('/trips/{trip}', [TripController::class, 'update'])->name('trips.update');
    Route::post('/trips/{trip}/accept', [TripController::class, 'accept'])->name('trips.accept');
    Route::delete('/trips/{trip}/decline', [TripController::class, 'decline'])->name('trips.decline');

    Route::get('/trips/{trip}/expenses', [ExpenseController::class, 'index'])->name('trips.expenses.index');
    Route::get('/trips/{trip}/expenses/create', [ExpenseController::class, 'create'])->name('trips.expenses.create');
    Route::post('/trips/{trip}/expenses', [ExpenseController::class, 'store'])->name('trips.expenses.store');
});

require __DIR__.'/auth.php';
