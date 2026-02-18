@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'flex items-center gap-3 p-4 rounded-xl bg-brand-purple/10 border border-brand-purple/20 text-brand-neon mb-6']) }}>
        <svg class="w-5 h-5 shrink-0 text-brand-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <span class="text-sm font-medium">{{ $status }}</span>
    </div>
@endif
