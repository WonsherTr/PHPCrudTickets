@extends('layouts.app')

@section('title', "Ticket #$ticket->id — HELPDESK LITE")

@section('content')
<div class="max-w-4xl mx-auto">

    {{-- Breadcrumb --}}
    <div class="mb-6 flex items-center gap-3">
        <a href="{{ route('tickets.index') }}" class="text-sm text-indigo-500 hover:text-indigo-700 font-medium transition">← Tickets</a>
        <span class="text-gray-300">/</span>
        <span class="text-sm text-gray-500">#{{ $ticket->id }}</span>
    </div>

    {{-- ── Header Card ── --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-6">
        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
            <div class="flex-1 min-w-0">
                <h1 class="text-2xl font-bold text-gray-900">{{ $ticket->title }}</h1>
                <div class="flex items-center gap-3 mt-2 text-sm text-gray-500">
                    <span>Por <strong>{{ $ticket->creator->name }}</strong></span>
                    <span>·</span>
                    <span>{{ $ticket->created_at->format('d M Y, H:i') }}</span>
                    @if($ticket->assignee)
                        <span>·</span>
                        <span>Asignado a <strong>{{ $ticket->assignee->name }}</strong></span>
                    @endif
                </div>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-{{ $ticket->status_color }}-50 text-{{ $ticket->status_color }}-700">
                    {{ $ticket->status }}
                </span>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-{{ $ticket->priority_color }}-50 text-{{ $ticket->priority_color }}-700">
                    {{ $ticket->priority }}
                </span>
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex flex-wrap gap-2 mt-4 pt-4 border-t border-gray-50">
            @can('update', $ticket)
            <a href="{{ route('tickets.edit', $ticket) }}"
               class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-medium bg-indigo-50 text-indigo-700 rounded-xl hover:bg-indigo-100 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Editar
            </a>
            @endcan
            @can('delete', $ticket)
            <form method="POST" action="{{ route('tickets.destroy', $ticket) }}" onsubmit="return confirm('¿Eliminar este ticket?')">
                @csrf @method('DELETE')
                <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-medium bg-red-50 text-red-700 rounded-xl hover:bg-red-100 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    Eliminar
                </button>
            </form>
            @endcan
        </div>
    </div>

    {{-- ── Description ── --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-6">
        <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-3">Descripción</h2>
        <div class="text-gray-700 leading-relaxed whitespace-pre-line">{{ $ticket->description }}</div>
    </div>

    {{-- ── Attachments Gallery ── --}}
    @if($ticket->attachments->isNotEmpty())
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-6">
        <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-4">
            Evidencias ({{ $ticket->attachments->count() }})
        </h2>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
            @foreach($ticket->attachments as $att)
            <div class="relative group aspect-square rounded-xl overflow-hidden border border-gray-100 shadow-sm">
                <img src="{{ $att->url }}" alt="{{ $att->original_name }}"
                     class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition"></div>
                <div class="absolute bottom-0 inset-x-0 bg-gradient-to-t from-black/50 p-2 opacity-0 group-hover:opacity-100 transition">
                    <p class="text-xs text-white truncate">{{ $att->original_name }}</p>
                </div>
                @can('update', $ticket)
                <form method="POST" action="{{ route('tickets.attachments.destroy', [$ticket, $att]) }}"
                      class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition"
                      onsubmit="return confirm('¿Eliminar esta imagen?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="w-7 h-7 bg-red-500 rounded-lg flex items-center justify-center text-white hover:bg-red-600 transition shadow-lg">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </form>
                @endcan
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- ── Add Attachments ── --}}
    @can('attach', $ticket)
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-6">
        <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-3">Adjuntar más imágenes</h2>
        <form method="POST" action="{{ route('tickets.attachments.store', $ticket) }}" enctype="multipart/form-data" class="flex items-end gap-3">
            @csrf
            <div class="flex-1">
                <input type="file" name="attachments[]" multiple accept="image/jpeg,image/png,image/webp"
                       class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:bg-indigo-50 file:text-indigo-700 file:font-medium hover:file:bg-indigo-100 file:cursor-pointer file:transition">
            </div>
            <button type="submit" class="bg-indigo-500 text-white px-5 py-2.5 rounded-xl text-sm font-medium hover:bg-indigo-600 transition shrink-0">
                Subir
            </button>
        </form>
    </div>
    @endcan

    {{-- ── Comments ── --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-4">
            Comentarios ({{ $ticket->comments->count() }})
        </h2>

        {{-- Comment list --}}
        @if($ticket->comments->isEmpty())
            <p class="text-gray-400 text-sm py-4">Aún no hay comentarios.</p>
        @else
            <div class="space-y-4 mb-6">
                @foreach($ticket->comments as $comment)
                <div class="flex gap-3">
                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-indigo-400 to-purple-500 flex items-center justify-center text-white text-xs font-semibold shrink-0">
                        {{ strtoupper(substr($comment->user->name, 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="bg-gray-50 rounded-xl p-4">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="text-sm font-semibold text-gray-900">{{ $comment->user->name }}</span>
                                @if($comment->user->isAdmin())
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-indigo-100 text-indigo-700">ADMIN</span>
                                @endif
                                <span class="text-xs text-gray-400">{{ $comment->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="text-sm text-gray-700 whitespace-pre-line">{{ $comment->body }}</p>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        @endif

        {{-- Add comment form --}}
        @can('comment', $ticket)
        <form method="POST" action="{{ route('tickets.comments.store', $ticket) }}" class="border-t border-gray-100 pt-4">
            @csrf
            <label for="body" class="sr-only">Comentario</label>
            <textarea id="body" name="body" rows="3" required
                      class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition resize-none"
                      placeholder="Escribe un comentario…"></textarea>
            <div class="flex justify-end mt-3">
                <button type="submit"
                        class="bg-gradient-to-r from-indigo-500 to-purple-600 text-white font-semibold py-2.5 px-6 rounded-xl text-sm hover:from-indigo-600 hover:to-purple-700 transition shadow-lg shadow-indigo-500/25">
                    Comentar
                </button>
            </div>
        </form>
        @endcan
    </div>

</div>
@endsection
