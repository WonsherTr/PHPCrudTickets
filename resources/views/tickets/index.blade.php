@extends('layouts.app')

@section('title', 'Tickets — HELPDESK LITE')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Tickets</h1>
        <p class="text-gray-500 mt-1">{{ $tickets->total() }} ticket(s) encontrado(s)</p>
    </div>
    <a href="{{ route('tickets.create') }}"
       class="inline-flex items-center gap-2 bg-gradient-to-r from-indigo-500 to-purple-600 text-white font-semibold py-2.5 px-5 rounded-xl hover:from-indigo-600 hover:to-purple-700 transition shadow-lg shadow-indigo-500/25 shrink-0">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Nuevo Ticket
    </a>
</div>

{{-- ── Search & Filters ── --}}
<form method="GET" action="{{ route('tickets.index') }}" class="bg-white rounded-2xl border border-gray-100 p-5 mb-8 shadow-sm">
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
        <div class="sm:col-span-2">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Buscar por título o descripción…"
                   class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
        </div>
        <div>
            <select name="status" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
                <option value="">Todos los estados</option>
                @foreach(\App\Models\Ticket::STATUSES as $s)
                    <option value="{{ $s }}" {{ request('status') == $s ? 'selected' : '' }}>{{ $s }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex gap-2">
            <select name="priority" class="flex-1 border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
                <option value="">Todas las prioridades</option>
                @foreach(\App\Models\Ticket::PRIORITIES as $p)
                    <option value="{{ $p }}" {{ request('priority') == $p ? 'selected' : '' }}>{{ $p }}</option>
                @endforeach
            </select>
            <button type="submit" class="bg-indigo-500 text-white px-4 py-2.5 rounded-xl hover:bg-indigo-600 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </button>
        </div>
    </div>
</form>

{{-- ── Ticket Cards ── --}}
@if($tickets->isEmpty())
    <div class="bg-white rounded-2xl border border-gray-100 p-12 text-center">
        <div class="w-16 h-16 bg-gray-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
        </div>
        <h3 class="text-lg font-semibold text-gray-900 mb-1">Sin tickets</h3>
        <p class="text-gray-500 text-sm">Crea tu primer ticket para empezar.</p>
    </div>
@else
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        @foreach($tickets as $ticket)
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-lg hover:border-indigo-100 transition group">
            <div class="p-5">
                {{-- Badges --}}
                <div class="flex items-center gap-2 mb-3">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-{{ $ticket->status_color }}-50 text-{{ $ticket->status_color }}-700">
                        {{ $ticket->status }}
                    </span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-{{ $ticket->priority_color }}-50 text-{{ $ticket->priority_color }}-700">
                        {{ $ticket->priority }}
                    </span>
                    <span class="text-xs text-gray-400 ml-auto">#{{ $ticket->id }}</span>
                </div>

                {{-- Title --}}
                <a href="{{ route('tickets.show', $ticket) }}" class="block">
                    <h3 class="font-semibold text-gray-900 group-hover:text-indigo-600 transition line-clamp-2">
                        {{ $ticket->title }}
                    </h3>
                </a>

                {{-- Description snippet --}}
                <p class="text-sm text-gray-500 mt-2 line-clamp-2">{{ Str::limit($ticket->description, 120) }}</p>

                {{-- Meta --}}
                <div class="flex items-center justify-between mt-4 pt-4 border-t border-gray-50">
                    <div class="flex items-center gap-2">
                        <div class="w-6 h-6 rounded-full bg-gradient-to-br from-indigo-400 to-purple-500 flex items-center justify-center text-white text-xs font-semibold">
                            {{ strtoupper(substr($ticket->creator->name, 0, 1)) }}
                        </div>
                        <span class="text-xs text-gray-500">{{ $ticket->creator->name }}</span>
                    </div>
                    <span class="text-xs text-gray-400">{{ $ticket->created_at->diffForHumans() }}</span>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Pagination --}}
    <div class="mt-8">
        {{ $tickets->links() }}
    </div>
@endif
@endsection
