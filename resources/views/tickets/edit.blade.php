@extends('layouts.app')

@section('title', "Editar Ticket #$ticket->id — HELPDESK LITE")

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-8">
        <a href="{{ route('tickets.show', $ticket) }}" class="text-sm text-indigo-500 hover:text-indigo-700 font-medium transition">← Volver al ticket</a>
        <h1 class="text-2xl font-bold text-gray-900 mt-2">Editar Ticket #{{ $ticket->id }}</h1>
    </div>

    @if($errors->any())
    <div class="bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl px-4 py-3 mb-6">
        <ul class="list-disc list-inside space-y-1">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form method="POST" action="{{ route('tickets.update', $ticket) }}" enctype="multipart/form-data"
          class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8 space-y-6">
        @csrf
        @method('PUT')

        <div>
            <label for="title" class="block text-sm font-medium text-gray-700 mb-1.5">Título</label>
            <input id="title" type="text" name="title" value="{{ old('title', $ticket->title) }}" required
                   class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
        </div>

        <div>
            <label for="description" class="block text-sm font-medium text-gray-700 mb-1.5">Descripción</label>
            <textarea id="description" name="description" rows="5" required
                      class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition resize-none">{{ old('description', $ticket->description) }}</textarea>
        </div>

        @if(auth()->user()->isAdmin())
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1.5">Estado</label>
                <select id="status" name="status"
                        class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
                    @foreach(\App\Models\Ticket::STATUSES as $s)
                        <option value="{{ $s }}" {{ old('status', $ticket->status) == $s ? 'selected' : '' }}>{{ $s }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="priority" class="block text-sm font-medium text-gray-700 mb-1.5">Prioridad</label>
                <select id="priority" name="priority"
                        class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
                    @foreach(\App\Models\Ticket::PRIORITIES as $p)
                        <option value="{{ $p }}" {{ old('priority', $ticket->priority) == $p ? 'selected' : '' }}>{{ $p }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        @endif

        {{-- Current attachments --}}
        @if($ticket->attachments->isNotEmpty())
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Imágenes actuales</label>
            <div class="grid grid-cols-4 gap-3">
                @foreach($ticket->attachments as $att)
                <div class="relative group aspect-square rounded-xl overflow-hidden border border-gray-100">
                    <img src="{{ $att->url }}" class="w-full h-full object-cover">
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Agregar más imágenes <span class="text-xs text-gray-400 font-normal">(opcional)</span></label>
            <input type="file" name="attachments[]" multiple accept="image/jpeg,image/png,image/webp"
                   class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:bg-indigo-50 file:text-indigo-700 file:font-medium hover:file:bg-indigo-100 file:cursor-pointer file:transition">
        </div>

        <div class="flex gap-3 pt-4">
            <button type="submit"
                    class="flex-1 bg-gradient-to-r from-indigo-500 to-purple-600 text-white font-semibold py-3 px-6 rounded-xl hover:from-indigo-600 hover:to-purple-700 transition shadow-lg shadow-indigo-500/25">
                Guardar cambios
            </button>
            <a href="{{ route('tickets.show', $ticket) }}"
               class="px-6 py-3 rounded-xl border border-gray-200 text-gray-600 font-medium hover:bg-gray-50 transition">
                Cancelar
            </a>
        </div>
    </form>
</div>
@endsection
