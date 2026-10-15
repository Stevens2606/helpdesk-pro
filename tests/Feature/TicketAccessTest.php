<?php

use App\Models\Category;
use App\Models\Ticket;
use App\Models\User;

it('lets a customer view their own ticket', function () {
    $owner  = User::factory()->create(['role' => 'customer']);
    $ticket = Ticket::factory()->for($owner, 'customer')->create();

    $this->actingAs($owner)
        ->get(route('tickets.show', $ticket))
        ->assertOk();
});

it('prevents a customer from viewing another customer ticket', function () {
    $owner  = User::factory()->create(['role' => 'customer']);
    $other  = User::factory()->create(['role' => 'customer']);
    $ticket = Ticket::factory()->for($owner, 'customer')->create();

    $this->actingAs($other)
        ->get(route('tickets.show', $ticket))
        ->assertForbidden();
});

it('lets staff view any ticket', function () {
    $staff  = User::factory()->create(['role' => 'staff']);
    $ticket = Ticket::factory()->create();

    $this->actingAs($staff)
        ->get(route('tickets.show', $ticket))
        ->assertOk();
});

it('prevents a customer from updating a ticket', function () {
    $owner  = User::factory()->create(['role' => 'customer']);
    $ticket = Ticket::factory()->for($owner, 'customer')->create();

    $this->actingAs($owner)
        ->put(route('tickets.update', $ticket), [
            'status' => 'closed', 'priority' => 'low',
        ])
        ->assertForbidden();
});

it('lets a customer create a ticket', function () {
    $customer = User::factory()->create(['role' => 'customer']);
    $category = Category::factory()->create();

    $this->actingAs($customer)
        ->post(route('tickets.store'), [
            'subject'     => 'Printer rusak',
            'description' => 'Tidak bisa mencetak.',
            'category_id' => $category->id,
            'priority'    => 'high',
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('tickets', [
        'subject' => 'Printer rusak',
        'user_id' => $customer->id,
    ]);
});

it('hides internal comments from customers', function () {
    $owner  = User::factory()->create(['role' => 'customer']);
    $staff  = User::factory()->create(['role' => 'staff']);
    $ticket = Ticket::factory()->for($owner, 'customer')->create();

    $ticket->comments()->create([
        'user_id' => $staff->id, 'body' => 'RAHASIA-INTERNAL', 'is_internal' => true,
    ]);

    $this->actingAs($owner)
        ->get(route('tickets.show', $ticket))
        ->assertOk()
        ->assertDontSee('RAHASIA-INTERNAL');
});

it('logs status changes', function () {
    $staff  = User::factory()->create(['role' => 'staff']);
    $ticket = Ticket::factory()->create();

    $this->actingAs($staff)->put(route('tickets.update', $ticket), [
        'status' => 'in_progress', 'priority' => 'medium',
    ]);

    $this->assertDatabaseHas('ticket_logs', [
        'ticket_id' => $ticket->id,
        'action'    => 'status_changed',
        'new_value' => 'in_progress',
    ]);
});