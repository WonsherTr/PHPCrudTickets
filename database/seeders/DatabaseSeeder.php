<?php

namespace Database\Seeders;

use App\Models\Ticket;
use App\Models\TicketAttachment;
use App\Models\TicketComment;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Core users ──
        $admin = User::create([
            'name'     => 'Admin',
            'email'    => 'admin@test.com',
            'password' => Hash::make('password'),
            'role'     => 'admin',
            'email_verified_at' => now(),
        ]);

        $user = User::create([
            'name'     => 'User Demo',
            'email'    => 'user@test.com',
            'password' => Hash::make('password'),
            'role'     => 'user',
            'email_verified_at' => now(),
        ]);

        // Extra users
        $extraUsers = User::factory(3)->create();

        // ── Tickets for the demo user ──
        $userTickets = Ticket::factory(5)->create([
            'created_by' => $user->id,
        ]);

        // ── Tickets for extra users ──
        $otherTickets = collect();
        foreach ($extraUsers as $eu) {
            $otherTickets = $otherTickets->merge(
                Ticket::factory(rand(2, 4))->create(['created_by' => $eu->id])
            );
        }

        $allTickets = $userTickets->merge($otherTickets);

        // ── Attachments (1-3 per ticket) ──
        foreach ($allTickets as $ticket) {
            TicketAttachment::factory(rand(1, 3))->create([
                'ticket_id' => $ticket->id,
            ]);
        }

        // ── Comments ──
        foreach ($allTickets as $ticket) {
            // Owner comments
            TicketComment::factory(rand(1, 2))->create([
                'ticket_id' => $ticket->id,
                'user_id'   => $ticket->created_by,
            ]);

            // Admin comments on some
            if (rand(0, 1)) {
                TicketComment::factory()->create([
                    'ticket_id' => $ticket->id,
                    'user_id'   => $admin->id,
                ]);
            }
        }
    }
}
