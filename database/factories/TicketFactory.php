<?php

namespace Database\Factories;

use App\Models\Ticket;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Ticket>
 */
class TicketFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
   public function definition(): array
    {
        return [
            'user_id'     => \App\Models\User::factory(),
            'category_id' => \App\Models\Category::factory(),
            'subject'     => fake()->sentence(4),
            'description' => fake()->paragraph(),
            'priority'    => 'medium',
            'status'      => 'open',
        ];
    }
}
