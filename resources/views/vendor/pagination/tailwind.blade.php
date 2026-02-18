@if ($paginator->hasPages())
    <div class="grid grid-cols-1 sm:grid-cols-3 items-center py-8 gap-4 px-2">
        {{-- Info Text (Left Aligned) --}}
        <div class="flex justify-center sm:justify-start">
            <p class="text-xs text-dark-text/40 font-medium tracking-wide">
                {!! __('Mostrando') !!}
                @if ($paginator->firstItem())
                    <span class="text-dark-text/60 px-0.5">{{ $paginator->firstItem() }}</span>
                    {!! __('al') !!}
                    <span class="text-dark-text/60 px-0.5">{{ $paginator->lastItem() }}</span>
                @else
                    {{ $paginator->count() }}
                @endif
                {!! __('de') !!}
                <span class="text-dark-text/60 px-0.5">{{ $paginator->total() }}</span>
                {!! __('resultados') !!}
            </p>
        </div>

        {{-- Centered Pagination Buttons --}}
        <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex justify-center items-center gap-1">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <span aria-disabled="true" aria-label="{{ __('pagination.previous') }}">
                    <span class="relative inline-flex items-center p-2 text-dark-text/20 cursor-not-allowed" aria-hidden="true">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path></svg>
                    </span>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="relative inline-flex items-center p-2 text-dark-text/60 hover:text-brand-lila transition-all duration-300 hover:scale-110 active:scale-90 group" aria-label="{{ __('pagination.previous') }}">
                    <svg class="w-5 h-5 transition-transform group-hover:-translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path></svg>
                </a>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <span aria-disabled="true">
                        <span class="relative inline-flex items-center px-3 py-2 text-xs font-bold text-dark-text/30 cursor-default">{{ $element }}</span>
                    </span>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span aria-current="page">
                                <span class="relative inline-flex items-center px-4 py-2 text-sm font-black text-white bg-brand-purple/20 border border-brand-purple/30 rounded-xl shadow-lg shadow-brand-purple/10 z-10 mx-1">{{ $page }}</span>
                            </span>
                        @else
                            <a href="{{ $url }}" class="relative inline-flex items-center px-4 py-2 text-sm font-bold text-dark-text/40 hover:text-white transition-all duration-300 hover:bg-dark-800/50 rounded-xl" aria-label="{{ __('Go to page :page', ['page' => $page]) }}">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="relative inline-flex items-center p-2 text-dark-text/60 hover:text-brand-lila transition-all duration-300 hover:scale-110 active:scale-90 group" aria-label="{{ __('pagination.next') }}">
                    <svg class="w-5 h-5 transition-transform group-hover:translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path></svg>
                </a>
            @else
                <span aria-disabled="true" aria-label="{{ __('pagination.next') }}">
                    <span class="relative inline-flex items-center p-2 text-dark-text/20 cursor-not-allowed" aria-hidden="true">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path></svg>
                    </span>
                </span>
            @endif
        </nav>

        {{-- Balanced Spacer (Empty div to match 3-column grid) --}}
        <div class="hidden sm:block"></div>
    </div>
@endif
