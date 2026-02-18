@props([
    'name' => 'confirm-deletion',
    'title' => '¿Estás seguro?',
    'message' => 'Esta acción no se puede deshacer.',
    'confirmText' => 'Confirmar',
    'cancelText' => 'Cancelar'
])

<div x-data="{ action: '' }" 
     x-on:open-deletion-modal.window="action = $event.detail.action; window.dispatchEvent(new CustomEvent('open-modal', { detail: '{{ $name }}' }))">
    <x-modal :name="$name" focusable>
        <div class="p-8 bg-white dark:bg-dark-850 relative overflow-hidden">
            <!-- Decoration Glow -->
            <div class="absolute top-0 right-0 -mr-16 -mt-16 w-64 h-64 bg-rose-500/10 rounded-full blur-3xl pointer-events-none"></div>

            <div class="relative z-10 flex flex-col items-center text-center">
                <div class="w-20 h-20 bg-linear-to-tr from-rose-500/20 to-rose-600/30 rounded-full flex items-center justify-center shadow-lg shadow-rose-500/10 mb-6 border border-rose-500/20">
                    <x-mary-icon name="o-exclamation-triangle" class="w-10 h-10 text-rose-500" />
                </div>

                <h3 class="text-2xl font-black text-gray-900 dark:text-white uppercase tracking-tight mb-2">
                    {{ $title }}
                </h3>
                
                <p class="text-gray-500 dark:text-gray-400 font-medium mb-8 text-lg">
                    {{ $message }}
                </p>

                <div class="flex flex-col sm:flex-row gap-3 w-full">
                    <button type="button" 
                            x-on:click="window.dispatchEvent(new CustomEvent('close-modal', { detail: '{{ $name }}' }))" 
                            class="flex-1 inline-flex justify-center items-center px-6 py-4 bg-gray-100 dark:bg-dark-800 border border-transparent rounded-xl font-bold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest hover:bg-gray-200 dark:hover:bg-dark-700 active:scale-95 transition-all duration-150 order-2 sm:order-1">
                        {{ $cancelText }}
                    </button>

                    <form x-bind:action="action" method="POST" class="flex-1 order-1 sm:order-2">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="w-full inline-flex justify-center items-center px-6 py-4 bg-linear-to-r from-rose-500 to-rose-600 border border-transparent rounded-xl font-black text-xs text-white uppercase tracking-widest hover:brightness-110 active:scale-95 transition-all duration-150 shadow-lg shadow-rose-500/20">
                            {{ $confirmText }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </x-modal>
</div>
