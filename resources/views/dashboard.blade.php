@extends('layouts.app')

@section('title', 'Dashboard — HELPDESK LITE')

@section('content')
<div class="mb-8">
    <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
    <p class="text-gray-500 mt-1">Bienvenido, {{ auth()->user()->name }}</p>
</div>

{{-- ── Stats Cards ── --}}
<div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4 mb-10">
    @php
        $statCards = [
            ['label' => 'Total',       'value' => $stats['total'],       'color' => 'indigo', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
            ['label' => 'Abiertos',    'value' => $stats['open'],        'color' => 'blue',   'icon' => 'M12 6v6m0 0v6m0-6h6m-6 0H6'],
            ['label' => 'En progreso', 'value' => $stats['in_progress'], 'color' => 'amber',  'icon' => 'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15'],
            ['label' => 'Resueltos',   'value' => $stats['resolved'],    'color' => 'emerald','icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
            ['label' => 'Cerrados',    'value' => $stats['closed'],      'color' => 'gray',   'icon' => 'M5 13l4 4L19 7'],
        ];
    @endphp

    @foreach($statCards as $card)
    <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm hover:shadow-md transition">
        <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 bg-{{ $card['color'] }}-50 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-{{ $card['color'] }}-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $card['icon'] }}"/>
                </svg>
            </div>
        </div>
        <p class="text-3xl font-bold text-gray-900">{{ $card['value'] }}</p>
        <p class="text-sm text-gray-500 mt-1">{{ $card['label'] }}</p>
    </div>
    @endforeach
</div>

{{-- ── Quick Actions ── --}}
<div class="flex flex-wrap gap-3 mb-10">
    <a href="{{ route('tickets.create') }}"
       class="inline-flex items-center gap-2 bg-gradient-to-r from-indigo-500 to-purple-600 text-white font-semibold py-2.5 px-5 rounded-xl hover:from-indigo-600 hover:to-purple-700 transition shadow-lg shadow-indigo-500/25">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Nuevo Ticket
    </a>
    <a href="{{ route('tickets.index') }}"
       class="inline-flex items-center gap-2 bg-white text-gray-700 font-medium py-2.5 px-5 rounded-xl border border-gray-200 hover:bg-gray-50 transition">
        Ver todos los tickets
    </a>
</div>

{{-- ── Recent Tickets ── --}}
<div>
    <h2 class="text-lg font-semibold text-gray-900 mb-4">Tickets recientes</h2>

    @if($recent->isEmpty())
        <div class="bg-white rounded-2xl border border-gray-100 p-8 text-center">
            <p class="text-gray-400">No hay tickets aún.</p>
        </div>
    @else
        <div class="space-y-3">
            @foreach($recent as $ticket)
            <a href="{{ route('tickets.show', $ticket) }}"
               class="block bg-white rounded-2xl border border-gray-100 p-5 hover:shadow-md hover:border-indigo-100 transition group">
                <div class="flex items-start justify-between gap-4">
                    <div class="min-w-0 flex-1">
                        <h3 class="font-semibold text-gray-900 group-hover:text-indigo-600 transition truncate">
                            #{{ $ticket->id }} — {{ $ticket->title }}
                        </h3>
                        <p class="text-sm text-gray-500 mt-1">
                            por {{ $ticket->creator->name }} · {{ $ticket->created_at->diffForHumans() }}
                        </p>
                    </div>
                    <div class="flex gap-2 shrink-0">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-{{ $ticket->status_color }}-50 text-{{ $ticket->status_color }}-700">
                            {{ $ticket->status }}
                        </span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-{{ $ticket->priority_color }}-50 text-{{ $ticket->priority_color }}-700">
                            {{ $ticket->priority }}
                        </span>
                    </div>
                </div>
            </a>
            @endforeach
        </div>
    @endif
</div>
@endsection
