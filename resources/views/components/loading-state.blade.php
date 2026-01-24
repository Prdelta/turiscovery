@props(['message' => 'Cargando...'])

<div class="col-span-full text-center py-12">
    <i data-lucide="loader" class="animate-spin mb-2 w-8 h-8 text-primary mx-auto"></i>
    <p class="text-slate-600">{{ $message }}</p>
</div>
