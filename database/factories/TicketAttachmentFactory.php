<?php

namespace Database\Factories;

use App\Models\Ticket;
use App\Models\TicketAttachment;
use Illuminate\Database\Eloquent\Factories\Factory;

class TicketAttachmentFactory extends Factory
{
    protected $model = TicketAttachment::class;

    public function definition(): array
    {
        return [
            'ticket_id' => Ticket::factory(),
            'file_path' => 'attachments/' . fake()->uuid() . '.png',
            'original_name' => fake()->word() . '.png',
            'mime_type' => 'image/png',
            'size' => fake()->numberBetween(10000, 5000000),
        ];
    }
}
