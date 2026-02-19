@extends('layouts.app')

@section('title', 'Nuevo Ticket — HELPDESK LITE')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-8">
        <a href="{{ route('tickets.index') }}" class="text-sm text-indigo-500 hover:text-indigo-700 font-medium transition">← Volver a tickets</a>
        <h1 class="text-2xl font-bold text-gray-900 mt-2">Nuevo Ticket</h1>
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

    <form method="POST" action="{{ route('tickets.store') }}" enctype="multipart/form-data"
          class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8 space-y-6">
        @csrf

        <div>
            <label for="title" class="block text-sm font-medium text-gray-700 mb-1.5">Título</label>
            <input id="title" type="text" name="title" value="{{ old('title') }}" required
                   class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                   placeholder="Describe brevemente el problema">
        </div>

        <div>
            <label for="description" class="block text-sm font-medium text-gray-700 mb-1.5">Descripción</label>
            <textarea id="description" name="description" rows="5" required
                      class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition resize-none"
                      placeholder="Detalla el problema, pasos para reproducir, etc.">{{ old('description') }}</textarea>
        </div>

        <div>
            <label for="priority" class="block text-sm font-medium text-gray-700 mb-1.5">Prioridad</label>
            <select id="priority" name="priority"
                    class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
                @foreach(\App\Models\Ticket::PRIORITIES as $p)
                    <option value="{{ $p }}" {{ old('priority', 'MEDIUM') == $p ? 'selected' : '' }}>{{ $p }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">
                Evidencias <span class="text-red-500">*</span>
                <span class="text-xs text-gray-400 font-normal">(jpg, png, webp — máx. 5 MB c/u)</span>
            </label>
            <div class="border-2 border-dashed border-gray-200 rounded-xl p-6 text-center hover:border-indigo-300 transition cursor-pointer" onclick="document.getElementById('attachments').click()">
                <svg class="w-10 h-10 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <p class="text-sm text-gray-500">Click para seleccionar imágenes</p>
                <input id="attachments" type="file" name="attachments[]" multiple accept="image/jpeg,image/png,image/webp" class="hidden">
            </div>
            <div id="preview-container" class="grid grid-cols-4 gap-3 mt-3"></div>
        </div>

        <div class="flex gap-3 pt-4">
            <button type="submit"
                    class="flex-1 bg-gradient-to-r from-indigo-500 to-purple-600 text-white font-semibold py-3 px-6 rounded-xl hover:from-indigo-600 hover:to-purple-700 transition shadow-lg shadow-indigo-500/25">
                Crear Ticket
            </button>
            <a href="{{ route('tickets.index') }}"
               class="px-6 py-3 rounded-xl border border-gray-200 text-gray-600 font-medium hover:bg-gray-50 transition">
                Cancelar
            </a>
        </div>
    </form>
</div>

<script>
document.getElementById('attachments').addEventListener('change', function(e) {
    const container = document.getElementById('preview-container');
    container.innerHTML = '';
    Array.from(e.target.files).forEach(file => {
        const reader = new FileReader();
        reader.onload = (ev) => {
            const div = document.createElement('div');
            div.className = 'relative aspect-square rounded-xl overflow-hidden border border-gray-100';
            div.innerHTML = `<img src="${ev.target.result}" class="w-full h-full object-cover">`;
            container.appendChild(div);
        };
        reader.readAsDataURL(file);
    });
});
</script>
@endsection
