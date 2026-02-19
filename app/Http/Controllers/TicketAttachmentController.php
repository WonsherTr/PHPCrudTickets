<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TicketAttachmentController extends Controller
{
    /**
     * Agregar adjuntos a un ticket existente.
     */
    public function store(Request $request, Ticket $ticket)
    {
        $this->authorize('attach', $ticket);

        $request->validate([
            'attachments'   => ['required', 'array', 'min:1'],
            'attachments.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        foreach ($request->file('attachments') as $file) {
            $path = $file->store('attachments', 'public');

            $ticket->attachments()->create([
                'file_path'     => $path,
                'original_name' => $file->getClientOriginalName(),
                'mime_type'     => $file->getClientMimeType(),
                'size'          => $file->getSize(),
            ]);
        }

        return back()->with('success', 'Imagen(es) adjuntada(s).');
    }

    /**
     * Eliminar un adjunto.
     */
    public function destroy(Ticket $ticket, TicketAttachment $attachment)
    {
        $this->authorize('update', $ticket);

        Storage::disk('public')->delete($attachment->file_path);
        $attachment->delete();

        return back()->with('success', 'Imagen eliminada.');
    }
}
