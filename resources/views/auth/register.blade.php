@extends('layouts.guest')

@section('title', 'Registro — HELPDESK LITE')

@section('content')
<h2 class="text-xl font-semibold text-white mb-6">Crear cuenta</h2>

@if($errors->any())
<div class="bg-red-500/10 border border-red-500/20 text-red-300 text-sm rounded-xl px-4 py-3 mb-4">
    <ul class="list-disc list-inside space-y-1">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<form method="POST" action="{{ route('register') }}" class="space-y-5">
    @csrf

    <div>
        <label for="name" class="block text-sm font-medium text-slate-300 mb-1.5">Nombre</label>
        <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
               class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
               placeholder="Tu nombre">
    </div>

    <div>
        <label for="email" class="block text-sm font-medium text-slate-300 mb-1.5">Email</label>
        <input id="email" type="email" name="email" value="{{ old('email') }}" required
               class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
               placeholder="tu@email.com">
    </div>

    <div>
        <label for="password" class="block text-sm font-medium text-slate-300 mb-1.5">Contraseña</label>
        <input id="password" type="password" name="password" required
               class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
               placeholder="Mínimo 8 caracteres">
    </div>

    <div>
        <label for="password_confirmation" class="block text-sm font-medium text-slate-300 mb-1.5">Confirmar contraseña</label>
        <input id="password_confirmation" type="password" name="password_confirmation" required
               class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
               placeholder="Repite tu contraseña">
    </div>

    <button type="submit"
            class="w-full bg-gradient-to-r from-indigo-500 to-purple-600 text-white font-semibold py-3 px-4 rounded-xl hover:from-indigo-600 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:ring-offset-slate-900 transition shadow-lg shadow-indigo-500/25">
        Crear cuenta
    </button>
</form>

<p class="mt-6 text-center text-sm text-slate-400">
    ¿Ya tienes cuenta?
    <a href="{{ route('login') }}" class="text-indigo-400 hover:text-indigo-300 font-medium transition">Inicia sesión</a>
</p>
@endsection
