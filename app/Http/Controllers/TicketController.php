<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTicketRequest;
use App\Http\Requests\UpdateTicketRequest;
use App\Models\Ticket;
use App\Models\TicketAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TicketController extends Controller
{
    /**
     * Lista de tickets con paginación, búsqueda y filtros.
     */
    public function index(Request $request)
    {
        $query = Ticket::with('creator');

        // Users see only their own tickets
        if (! $request->user()->isAdmin()) {
            $query->where('created_by', $request->user()->id);
        }

        $query->search($request->input('search'))
              ->filterStatus($request->input('status'))
              ->filterPriority($request->input('priority'));

        $tickets = $query->latest()->paginate(12)->withQueryString();

        return view('tickets.index', compact('tickets'));
    }

    /**
     * Formulario de creación.
     */
    public function create()
    {
        return view('tickets.create');
    }

    /**
     * Guardar ticket + imágenes.
     */
    public function store(StoreTicketRequest $request)
    {
        $ticket = Ticket::create([
            'title'       => $request->title,
            'description' => $request->description,
            'priority'    => $request->priority,
            'status'      => 'OPEN',
            'created_by'  => $request->user()->id,
        ]);

        // Attachments
        foreach ($request->file('attachments') as $file) {
            $path = $file->store('attachments', 'public');

            $ticket->attachments()->create([
                'file_path'     => $path,
                'original_name' => $file->getClientOriginalName(),
                'mime_type'     => $file->getClientMimeType(),
                'size'          => $file->getSize(),
            ]);
        }

        return redirect()->route('tickets.show', $ticket)
            ->with('success', 'Ticket creado exitosamente.');
    }

    /**
     * Detalle de ticket con comentarios y adjuntos.
     */
    public function show(Ticket $ticket)
    {
        $this->authorize('view', $ticket);

        $ticket->load(['attachments', 'comments.user', 'creator', 'assignee']);

        return view('tickets.show', compact('ticket'));
    }

    /**
     * Formulario de edición.
     */
    public function edit(Ticket $ticket)
    {
        $this->authorize('update', $ticket);

        $ticket->load('attachments');

        return view('tickets.edit', compact('ticket'));
    }

    /**
     * Actualizar ticket.
     */
    public function update(UpdateTicketRequest $request, Ticket $ticket)
    {
        $this->authorize('update', $ticket);

        $data = [
            'title'       => $request->title,
            'description' => $request->description,
        ];

        if ($request->user()->isAdmin()) {
            $data['status']   = $request->status;
            $data['priority'] = $request->priority;
        }

        $ticket->update($data);

        // New attachments
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('attachments', 'public');

                $ticket->attachments()->create([
                    'file_path'     => $path,
                    'original_name' => $file->getClientOriginalName(),
                    'mime_type'     => $file->getClientMimeType(),
                    'size'          => $file->getSize(),
                ]);
            }
        }

        return redirect()->route('tickets.show', $ticket)
            ->with('success', 'Ticket actualizado.');
    }

    /**
     * Eliminar ticket.
     */
    public function destroy(Ticket $ticket)
    {
        $this->authorize('delete', $ticket);

        // Delete files from storage
        foreach ($ticket->attachments as $att) {
            Storage::disk('public')->delete($att->file_path);
        }

        $ticket->delete();

        return redirect()->route('tickets.index')
            ->with('success', 'Ticket eliminado.');
    }
}
