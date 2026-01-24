@props(['icon' => 'inbox', 'message' => 'No hay elementos disponibles'])

<div class="col-span-full text-center py-12">
    <i data-lucide="{{ $icon }}" class="w-12 h-12 text-slate-300 mx-auto mb-4"></i>
    <p class="text-slate-500">{{ $message }}</p>
</div>
