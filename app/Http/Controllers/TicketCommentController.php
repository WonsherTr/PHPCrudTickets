<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketComment;
use Illuminate\Http\Request;

class TicketCommentController extends Controller
{
    /**
     * Agregar comentario a un ticket.
     */
    public function store(Request $request, Ticket $ticket)
    {
        $this->authorize('comment', $ticket);

        $request->validate([
            'body' => ['required', 'string', 'max:2000'],
        ]);

        $ticket->comments()->create([
            'user_id' => $request->user()->id,
            'body'    => $request->body,
        ]);

        return back()->with('success', 'Comentario agregado.');
    }
}
