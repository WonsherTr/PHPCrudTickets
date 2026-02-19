@extends('layouts.guest')

@section('title', 'Iniciar sesión — HELPDESK LITE')

@section('content')
<h2 class="text-xl font-semibold text-white mb-6">Iniciar sesión</h2>

@if($errors->any())
<div class="bg-red-500/10 border border-red-500/20 text-red-300 text-sm rounded-xl px-4 py-3 mb-4">
    {{ $errors->first() }}
</div>
@endif

<form method="POST" action="{{ route('login') }}" class="space-y-5">
    @csrf

    <div>
        <label for="email" class="block text-sm font-medium text-slate-300 mb-1.5">Email</label>
        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
               class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
               placeholder="tu@email.com">
    </div>

    <div>
        <label for="password" class="block text-sm font-medium text-slate-300 mb-1.5">Contraseña</label>
        <input id="password" type="password" name="password" required
               class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
               placeholder="••••••••">
    </div>

    <div class="flex items-center">
        <input id="remember" type="checkbox" name="remember"
               class="w-4 h-4 rounded bg-white/10 border-white/20 text-indigo-500 focus:ring-indigo-500 focus:ring-offset-0">
        <label for="remember" class="ml-2 text-sm text-slate-400">Recordarme</label>
    </div>

    <button type="submit"
            class="w-full bg-gradient-to-r from-indigo-500 to-purple-600 text-white font-semibold py-3 px-4 rounded-xl hover:from-indigo-600 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:ring-offset-slate-900 transition shadow-lg shadow-indigo-500/25">
        Ingresar
    </button>
</form>

<p class="mt-6 text-center text-sm text-slate-400">
    ¿No tienes cuenta?
    <a href="{{ route('register') }}" class="text-indigo-400 hover:text-indigo-300 font-medium transition">Regístrate</a>
</p>
@endsection
