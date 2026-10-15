<?php

use App\Models\Ticket;

it('flags overdue tickets only once', function () {
    $ticket = Ticket::factory()->create();
    $ticket->update(['due_at' => now()->subDay()]);

    $this->artisan('tickets:check-overdue')
        ->expectsOutput('1 tiket ditandai melewati SLA.')
        ->assertSuccessful();

    expect($ticket->fresh()->sla_breached_at)->not->toBeNull();

    $this->artisan('tickets:check-overdue')
        ->expectsOutput('0 tiket ditandai melewati SLA.');
});

it('ignores resolved tickets', function () {
    $ticket = Ticket::factory()->create(['status' => 'resolved']);
    $ticket->update(['due_at' => now()->subDay()]);

    $this->artisan('tickets:check-overdue')
        ->expectsOutput('0 tiket ditandai melewati SLA.');
});