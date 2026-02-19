<?php

namespace Database\Factories;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TicketFactory extends Factory
{
    protected $model = Ticket::class;

    public function definition(): array
    {
        return [
            'title' => fake()->sentence(6),
            'description' => fake()->paragraphs(2, true),
            'status' => fake()->randomElement(Ticket::STATUSES),
            'priority' => fake()->randomElement(Ticket::PRIORITIES),
            'created_by' => User::factory(),
            'assigned_to' => null,
        ];
    }

    public function open(): static
    {
        return $this->state(fn () => ['status' => 'OPEN']);
    }

    public function urgent(): static
    {
        return $this->state(fn () => ['priority' => 'URGENT']);
    }
}
