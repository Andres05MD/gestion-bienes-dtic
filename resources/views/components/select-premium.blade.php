@props([
'label' => null,
'name',
'options' => [],
'icon' => null,
'placeholder' => 'Seleccione una opción',
'required' => false,
'value' => null,
'class' => '',
'searchable' => true,
])

<div {{ $attributes->merge(['class' => "space-y-2 $class transition-all duration-300 relative"]) }}
    :class="open ? 'z-50' : 'z-0'"
    x-data="{ 
        open: false, 
        selected: @js($value),
        search: '',
        allOptions: @js($options),
        get filteredOptions() {
            if (!this.search) return this.allOptions;
            return this.allOptions.filter(o => 
                o.label.toLowerCase().includes(this.search.toLowerCase())
            );
        },
        selectedLabel() {
            const option = this.allOptions.find(o => o.value == this.selected);
            return option ? option.label : '{{ $placeholder }}';
        },
        select(val) {
            this.selected = val;
            this.open = false;
            this.search = '';
            this.$dispatch('option-selected', val);
            this.$dispatch('change', val);
        }
     }"
    @click.away="open = false; search = ''"
    @set-selected-{{ str_replace('_', '-', $name) }}.window="selected = $event.detail">

    @if($label)
    <label for="{{ $name }}" class="text-[10px] font-bold text-gray-500 dark:text-gray-300 uppercase tracking-[0.2em] ml-1">
        {{ $label }} @if($required)<span class="text-brand-purple">*</span>@endif
    </label>
    @endif

    <div class="relative group">
        <!-- Input Simulado -->
        <button
            type="button"
            @click="open = !open; if(open) setTimeout(() => $refs.searchInput.focus(), 100)"
            class="relative w-full flex items-center {{ $icon ? 'pl-11' : 'pl-5' }} pr-12 py-4 {{ $label ? 'h-14' : 'h-12' }} bg-white dark:bg-[#1a1a1a] border border-gray-200 dark:border-white/5 rounded-2xl text-left text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand-purple/20 transition-all duration-300 shadow-sm dark:shadow-none hover:bg-gray-50 dark:hover:bg-[#222] cursor-pointer"
            :class="{'ring-2 ring-brand-purple/20 bg-gray-50 dark:bg-[#222]': open}">
            @if($icon)
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                <x-mary-icon :name="$icon" class="w-5 h-5 text-gray-400 dark:text-gray-500 group-hover:text-brand-purple transition-colors duration-300" x-bind:class="{'text-brand-purple': open}" />
            </div>
            @endif

            <span class="flex-1 min-w-0 block truncate font-medium text-sm" :class="{'text-gray-400 dark:text-gray-500': !selected, 'text-gray-900 dark:text-white': selected}" x-text="selectedLabel()"></span>

            <span class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none transition-transform duration-300" :class="{'rotate-180': open}">
                <x-mary-icon name="o-chevron-down" class="w-4 h-4 text-gray-400" />
            </span>
        </button>

        <!-- Input oculto para validación nativa del formulario -->
        <input type="text" name="{{ $name }}" :value="selected" class="absolute w-0 h-0 opacity-0 pointer-events-none" tabindex="-1" @if($required) required @endif>

        <!-- Dropdown Panel -->
        <div
            x-show="open"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-95 -translate-y-2"
            class="absolute z-50 w-full mt-2 bg-white dark:bg-[#1a1a1a] border border-gray-100 dark:border-white/10 rounded-2xl shadow-2xl dark:shadow-[0_20px_50px_rgba(0,0,0,0.5)] backdrop-blur-xl overflow-hidden"
            style="display: none;">
            @if($searchable)
            <div class="p-3 border-b border-gray-100 dark:border-white/5">
                <div class="relative">
                    <x-mary-icon name="o-magnifying-glass" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
                    <input
                        x-ref="searchInput"
                        x-model="search"
                        type="text"
                        placeholder="Buscar..."
                        class="w-full pl-9 pr-4 py-2 bg-gray-50 dark:bg-dark-900/50 border-none rounded-xl text-xs text-gray-900 dark:text-white focus:ring-2 focus:ring-brand-purple/20 transition-all font-medium"
                        @click.stop>
                </div>
            </div>
            @endif

            <div class="max-h-60 overflow-y-auto py-2 custom-scrollbar">
                <template x-for="option in filteredOptions" :key="option.value">
                    <button
                        type="button"
                        @click="select(option.value)"
                        class="w-full flex items-center px-4 py-3 text-sm transition-all duration-200 hover:bg-brand-purple/5 dark:hover:bg-brand-purple/10 group relative"
                        :class="{'bg-brand-purple/10 text-brand-purple font-bold': selected == option.value, 'text-gray-700 dark:text-gray-300': selected != option.value}">
                        <!-- Indicador de seleccionado -->
                        <div x-show="selected == option.value" class="absolute left-0 w-1 h-6 bg-brand-purple rounded-r-full"></div>

                        <span class="ml-2" x-text="option.label"></span>

                        <x-mary-icon
                            x-show="selected == option.value"
                            name="o-check"
                            class="ml-auto w-4 h-4 text-brand-purple" />
                    </button>
                </template>

                <div x-show="filteredOptions.length === 0" class="px-4 py-8 text-center text-gray-500">
                    <x-mary-icon name="o-inbox" class="w-8 h-8 mx-auto mb-2 opacity-20" />
                    <p class="text-[10px] uppercase tracking-widest">Sin resultados para "<span x-text="search"></span>"</p>
                </div>
            </div>
        </div>
    </div>

    <x-input-error :messages="$errors->get($name)" class="mt-1" />
</div>