<?php

namespace Tests\Feature;

use App\Models\Ticket;
use App\Models\TicketAttachment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TicketTest extends TestCase
{
    use RefreshDatabase;

    /* ──────────────────────────────────────────
     *  CREATE
     * ────────────────────────────────────────── */

    public function test_user_can_create_ticket_with_image(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('tickets.store'), [
            'title'       => 'Mi primer ticket',
            'description' => 'Descripción del problema',
            'priority'    => 'HIGH',
            'attachments' => [
                UploadedFile::fake()->image('evidence.png', 800, 600)->size(1024),
            ],
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('tickets', [
            'title'      => 'Mi primer ticket',
            'created_by' => $user->id,
            'status'     => 'OPEN',
        ]);

        $this->assertDatabaseCount('ticket_attachments', 1);

        Storage::disk('public')->assertExists(
            TicketAttachment::first()->file_path
        );
    }

    public function test_ticket_requires_at_least_one_image(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('tickets.store'), [
            'title'       => 'No images',
            'description' => 'Falta evidencia',
            'priority'    => 'LOW',
        ]);

        $response->assertSessionHasErrors('attachments');
    }

    /* ──────────────────────────────────────────
     *  VIEW / AUTHORIZATION
     * ────────────────────────────────────────── */

    public function test_user_can_see_own_tickets(): void
    {
        $user = User::factory()->create();
        $ticket = Ticket::factory()->create(['created_by' => $user->id]);

        $response = $this->actingAs($user)->get(route('tickets.show', $ticket));
        $response->assertStatus(200);
        $response->assertSee($ticket->title);
    }

    public function test_user_cannot_see_other_users_ticket(): void
    {
        $user  = User::factory()->create();
        $other = User::factory()->create();
        $ticket = Ticket::factory()->create(['created_by' => $other->id]);

        $response = $this->actingAs($user)->get(route('tickets.show', $ticket));
        $response->assertStatus(403);
    }

    public function test_admin_can_see_all_tickets(): void
    {
        $admin = User::factory()->admin()->create();
        $user  = User::factory()->create();
        $ticket = Ticket::factory()->create(['created_by' => $user->id]);

        $response = $this->actingAs($admin)->get(route('tickets.show', $ticket));
        $response->assertStatus(200);
    }

    public function test_admin_sees_all_tickets_in_index(): void
    {
        $admin = User::factory()->admin()->create();
        $user  = User::factory()->create();

        Ticket::factory(3)->create(['created_by' => $user->id]);

        $response = $this->actingAs($admin)->get(route('tickets.index'));
        $response->assertStatus(200);
    }

    /* ──────────────────────────────────────────
     *  UPDATE / STATUS / PRIORITY
     * ────────────────────────────────────────── */

    public function test_admin_can_change_status_and_priority(): void
    {
        $admin  = User::factory()->admin()->create();
        $ticket = Ticket::factory()->create(['status' => 'OPEN', 'priority' => 'LOW']);

        $response = $this->actingAs($admin)->put(route('tickets.update', $ticket), [
            'title'       => $ticket->title,
            'description' => $ticket->description,
            'status'      => 'RESOLVED',
            'priority'    => 'URGENT',
        ]);

        $response->assertRedirect();

        $ticket->refresh();
        $this->assertEquals('RESOLVED', $ticket->status);
        $this->assertEquals('URGENT', $ticket->priority);
    }

    public function test_user_cannot_change_status(): void
    {
        $user = User::factory()->create();
        $ticket = Ticket::factory()->create([
            'created_by' => $user->id,
            'status'     => 'OPEN',
        ]);

        $this->actingAs($user)->put(route('tickets.update', $ticket), [
            'title'       => $ticket->title,
            'description' => $ticket->description,
            'status'      => 'CLOSED',
        ]);

        $ticket->refresh();
        $this->assertEquals('OPEN', $ticket->status);
    }

    /* ──────────────────────────────────────────
     *  DELETE
     * ────────────────────────────────────────── */

    public function test_admin_can_delete_any_ticket(): void
    {
        $admin  = User::factory()->admin()->create();
        $ticket = Ticket::factory()->create();

        $response = $this->actingAs($admin)->delete(route('tickets.destroy', $ticket));
        $response->assertRedirect(route('tickets.index'));
        $this->assertDatabaseMissing('tickets', ['id' => $ticket->id]);
    }

    public function test_user_can_delete_own_ticket(): void
    {
        $user = User::factory()->create();
        $ticket = Ticket::factory()->create(['created_by' => $user->id]);

        $response = $this->actingAs($user)->delete(route('tickets.destroy', $ticket));
        $response->assertRedirect(route('tickets.index'));
    }

    public function test_user_cannot_delete_others_ticket(): void
    {
        $user  = User::factory()->create();
        $other = User::factory()->create();
        $ticket = Ticket::factory()->create(['created_by' => $other->id]);

        $response = $this->actingAs($user)->delete(route('tickets.destroy', $ticket));
        $response->assertStatus(403);
    }
}
