@props([
    'text' => '',
    'icon' => null,
    'color' => 'purple', // purple, rose, sky, dark
    'type' => 'button',
])

@php
    $colorClasses = match($color) {
        'purple' => 'bg-brand-purple hover:bg-brand-lila shadow-brand-purple/20 text-white',
        'rose' => 'bg-rose-500 hover:bg-rose-400 shadow-rose-500/20 text-white',
        'sky' => 'bg-sky-500 hover:bg-sky-400 shadow-sky-500/20 text-white',
        'dark' => 'bg-dark-800 border border-dark-700 text-white hover:bg-dark-700',
        default => 'bg-brand-purple hover:bg-brand-lila shadow-brand-purple/20 text-white',
    };
@endphp

<button {{ $attributes->merge(['type' => $type, 'class' => "inline-flex items-center px-6 py-3 rounded-xl font-black text-xs uppercase tracking-widest active:scale-95 transition-all duration-300 shadow-lg $colorClasses"]) }}>
    @if($icon)
        <x-mary-icon :name="$icon" class="w-4 h-4 mr-2" />
    @endif
    {{ $text ?: $slot }}
</button>
