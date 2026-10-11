<?php
use App\Http\Controllers\TicketController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('tickets', TicketController::class)->except(['edit']);
});