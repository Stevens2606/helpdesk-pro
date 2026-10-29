<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schedule;

Route::view('/', 'welcome');

Route::get('dashboard', \App\Http\Controllers\DashboardController::class)
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

Schedule::command('tickets:check-overdue')->hourly()->withoutOverlapping();

use App\Http\Controllers\ReportController;

Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
Route::get('reports/export', [ReportController::class, 'export'])->name('reports.export');

