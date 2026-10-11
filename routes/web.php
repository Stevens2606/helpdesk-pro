<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__.'/auth.php';
use App\Http\Controllers\TicketController;
use App\Http\Controllers\TicketCommentController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('tickets', TicketController::class)->except(['edit']);
    Route::post('tickets/{ticket}/comments', [TicketCommentController::class, 'store'])
        ->name('tickets.comments.store');
});