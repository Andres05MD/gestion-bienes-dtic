@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex items-center justify-between py-6">
        @if ($paginator->onFirstPage())
            <span class="relative inline-flex items-center px-6 py-2.5 text-sm font-bold text-dark-text bg-dark-850 border border-dark-800 cursor-not-allowed rounded-xl transition-all duration-300 opacity-50">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path></svg>
                {!! __('Anterior') !!}
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="relative inline-flex items-center px-6 py-2.5 text-sm font-bold text-white bg-dark-850 border border-dark-800 rounded-xl hover:bg-brand-purple hover:border-brand-purple transition-all duration-300 active:scale-95 shadow-lg shadow-black/20 group">
                <svg class="w-5 h-5 mr-2 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path></svg>
                {!! __('Anterior') !!}
            </a>
        @endif

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="relative inline-flex items-center px-6 py-2.5 text-sm font-bold text-white bg-dark-850 border border-dark-800 rounded-xl hover:bg-brand-purple hover:border-brand-purple transition-all duration-300 active:scale-95 shadow-lg shadow-black/20 group">
                {!! __('Siguiente') !!}
                <svg class="w-5 h-5 ml-2 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path></svg>
            </a>
        @else
            <span class="relative inline-flex items-center px-6 py-2.5 text-sm font-bold text-dark-text bg-dark-850 border border-dark-800 cursor-not-allowed rounded-xl transition-all duration-300 opacity-50">
                {!! __('Siguiente') !!}
                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path></svg>
            </span>
        @endif
    </nav>
@endif
