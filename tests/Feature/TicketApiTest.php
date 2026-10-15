<?php

use App\Models\Category;
use App\Models\Ticket;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

it('rejects unauthenticated requests', function () {
    $this->getJson('/api/v1/tickets')->assertUnauthorized();
});

it('only lists a customer own tickets', function () {
    $me = User::factory()->create(['role' => 'customer']);
    Ticket::factory()->for($me, 'customer')->count(2)->create();
    Ticket::factory()->count(3)->create();

    Sanctum::actingAs($me);

    $this->getJson('/api/v1/tickets')
        ->assertOk()
        ->assertJsonCount(2, 'data');
});

it('creates a ticket through the API', function () {
    Sanctum::actingAs(User::factory()->create(['role' => 'customer']));
    $category = Category::factory()->create();

    $this->postJson('/api/v1/tickets', [
        'subject' => 'Via API', 'description' => 'Uji.',
        'category_id' => $category->id, 'priority' => 'low',
    ])->assertCreated()->assertJsonPath('data.subject', 'Via API');
});

it('returns 403 for another customer ticket', function () {
    $ticket = Ticket::factory()->create();
    Sanctum::actingAs(User::factory()->create(['role' => 'customer']));

    $this->getJson("/api/v1/tickets/{$ticket->id}")->assertForbidden();
});