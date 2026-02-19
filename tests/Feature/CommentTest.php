<?php

namespace Tests\Feature;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_comment_on_own_ticket(): void
    {
        $user   = User::factory()->create();
        $ticket = Ticket::factory()->create(['created_by' => $user->id]);

        $response = $this->actingAs($user)->post(
            route('tickets.comments.store', $ticket),
            ['body' => 'Este es un comentario de prueba']
        );

        $response->assertRedirect();
        $this->assertDatabaseHas('ticket_comments', [
            'ticket_id' => $ticket->id,
            'user_id'   => $user->id,
            'body'      => 'Este es un comentario de prueba',
        ]);
    }

    public function test_user_cannot_comment_on_others_ticket(): void
    {
        $user  = User::factory()->create();
        $other = User::factory()->create();
        $ticket = Ticket::factory()->create(['created_by' => $other->id]);

        $response = $this->actingAs($user)->post(
            route('tickets.comments.store', $ticket),
            ['body' => 'Intento de comentario']
        );

        $response->assertStatus(403);
    }

    public function test_admin_can_comment_on_any_ticket(): void
    {
        $admin  = User::factory()->admin()->create();
        $user   = User::factory()->create();
        $ticket = Ticket::factory()->create(['created_by' => $user->id]);

        $response = $this->actingAs($admin)->post(
            route('tickets.comments.store', $ticket),
            ['body' => 'Comentario del admin']
        );

        $response->assertRedirect();
        $this->assertDatabaseHas('ticket_comments', [
            'ticket_id' => $ticket->id,
            'user_id'   => $admin->id,
        ]);
    }

    public function test_comment_requires_body(): void
    {
        $user   = User::factory()->create();
        $ticket = Ticket::factory()->create(['created_by' => $user->id]);

        $response = $this->actingAs($user)->post(
            route('tickets.comments.store', $ticket),
            ['body' => '']
        );

        $response->assertSessionHasErrors('body');
    }
}
