@props([
    'label' => null,
    'name',
    'icon' => 'o-calendar',
    'placeholder' => 'Seleccione una fecha',
    'required' => false,
    'value' => '',
])

@php
    $months = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
    $days = ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'];
@endphp

<div class="space-y-2 transition-all duration-300 relative" 
    :class="show ? 'z-50' : 'z-0'"
    x-data="{
        show: false,
        isUp: false,
        selectedDate: @js(old($name, $value)),
        displayText: '',
        month: '',
        year: '',
        no_of_days: [],
        blankdays: [],
        days: @js($days),
        months: @js($months),

        initDate() {
            let today = this.selectedDate ? new Date(this.selectedDate + 'T00:00:00') : new Date();
            this.month = today.getMonth();
            this.year = today.getFullYear();
            if (this.selectedDate) {
                this.formatDateForDisplay();
            }
        },

        toggleCalendar() {
            if (!this.show) {
                // Calcular espacio disponible
                const rect = this.$el.getBoundingClientRect();
                const windowHeight = window.innerHeight;
                const calendarHeight = 420; // Altura aproximada del calendario con p-4 y mt-2
                
                // Si el espacio abajo es menor a la altura del calendario, desplegar hacia arriba
                this.isUp = (windowHeight - rect.bottom) < calendarHeight;
            }
            this.show = !this.show;
        },

        formatDateForDisplay() {
            if (!this.selectedDate) return;
            let date = new Date(this.selectedDate + 'T00:00:00');
            this.displayText = date.toLocaleDateString('es-ES', { day: '2-digit', month: 'long', year: 'numeric' });
        },

        isToday(date) {
            const today = new Date();
            const d = new Date(this.year, this.month, date);
            return today.toDateString() === d.toDateString();
        },

        isSelected(date) {
            const d = new Date(this.year, this.month, date);
            const selected = this.selectedDate ? new Date(this.selectedDate + 'T00:00:00') : null;
            return selected ? selected.toDateString() === d.toDateString() : false;
        },

        getNoOfDays() {
            let daysInMonth = new Date(this.year, this.month + 1, 0).getDate();
            let dayOfWeek = new Date(this.year, this.month).getDay();
            let blankdaysArray = [];
            for (var i = 1; i <= dayOfWeek; i++) {
                blankdaysArray.push(i);
            }
            let daysArray = [];
            for (var i = 1; i <= daysInMonth; i++) {
                daysArray.push(i);
            }
            this.blankdays = blankdaysArray;
            this.no_of_days = daysArray;
        },

        selectDate(date) {
            let d = new Date(this.year, this.month, date);
            this.selectedDate = d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0') + '-' + String(d.getDate()).padStart(2, '0');
            this.formatDateForDisplay();
            this.show = false;
        },

        previousMonth() {
            if (this.month == 0) {
                this.month = 11;
                this.year--;
            } else {
                this.month--;
            }
            this.getNoOfDays();
        },

        nextMonth() {
            if (this.month == 11) {
                this.month = 0;
                this.year++;
            } else {
                this.month++;
            }
            this.getNoOfDays();
        }
    }" 
    x-init="initDate(); getNoOfDays()"
    @click.away="show = false">
    
    @if($label)
        <label for="{{ $name }}" class="text-[10px] font-bold text-gray-500 dark:text-gray-300 uppercase tracking-[0.2em] ml-1">
            {{ $label }} @if($required)<span class="text-brand-purple">*</span>@endif
        </label>
    @endif
    
    <div class="relative group">
        @if($icon)
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none z-10 transition-colors duration-300" :class="show ? 'text-brand-purple' : 'text-gray-400 dark:text-gray-500'">
                <x-mary-icon :name="$icon" class="w-5 h-5" />
            </div>
        @endif

        <input type="hidden" name="{{ $name }}" x-model="selectedDate" @if($required) required @endif>
        
        <div 
            @click="toggleCalendar()"
            class="block w-full cursor-pointer {{ $icon ? 'pl-11' : 'pl-4' }} pr-4 py-4 h-14 bg-white dark:bg-[#1a1a1a] border border-gray-200 dark:border-white/5 rounded-2xl text-gray-900 dark:text-white transition-all duration-300 shadow-sm dark:shadow-none relative flex items-center"
            :class="show ? 'ring-2 ring-brand-purple/20 bg-gray-50 dark:bg-[#222]' : 'hover:bg-gray-50 dark:hover:bg-[#222]'"
        >
            <span x-text="displayText || '{{ $placeholder }}'" :class="!displayText && 'text-gray-400 dark:text-gray-500'" class="text-sm font-medium"></span>
            
            <div class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 transition-transform duration-300" :class="show && 'rotate-180 text-brand-purple'">
                <x-mary-icon name="o-chevron-down" class="w-4 h-4" />
            </div>
        </div>

        <!-- Calendario Popover -->
        <div 
            x-show="show"
            x-transition:enter="transition ease-out duration-200"
            :x-transition:enter-start="isUp ? 'opacity-0 scale-95 translate-y-2' : 'opacity-0 scale-95 -translate-y-2'"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            :x-transition:leave-end="isUp ? 'opacity-0 scale-95 translate-y-2' : 'opacity-0 scale-95 -translate-y-2'"
            class="absolute z-[100] p-4 bg-white/80 dark:bg-dark-900/90 backdrop-blur-xl border border-gray-100 dark:border-white/10 rounded-4xl shadow-2xl w-76 sm:w-88"
            :class="isUp ? 'bottom-full mb-2' : 'top-full mt-2'"
            style="display: none;"
        >
            <!-- Header Calendario -->
            <div class="flex items-center justify-between mb-4 px-2">
                <button type="button" @click="previousMonth()" class="p-2 hover:bg-brand-purple/10 rounded-xl transition-colors text-gray-500 dark:text-gray-400 hover:text-brand-purple">
                    <x-mary-icon name="o-chevron-left" class="w-4 h-4" />
                </button>
                <div class="text-center">
                    <span x-text="months[month]" class="text-xs font-black uppercase tracking-[0.2em] text-gray-900 dark:text-white"></span>
                    <span x-text="year" class="text-xs font-medium text-brand-lila ml-1"></span>
                </div>
                <button type="button" @click="nextMonth()" class="p-2 hover:bg-brand-purple/10 rounded-xl transition-colors text-gray-500 dark:text-gray-400 hover:text-brand-purple">
                    <x-mary-icon name="o-chevron-right" class="w-4 h-4" />
                </button>
            </div>

            <!-- Días de la semana -->
            <div class="grid grid-cols-7 gap-1 mb-2">
                <template x-for="day in days" :key="day">
                    <div class="text-center">
                        <span x-text="day" class="text-[9px] font-bold text-gray-400 dark:text-gray-600 uppercase tracking-tighter"></span>
                    </div>
                </template>
            </div>

            <!-- Cuadrícula de días -->
            <div class="grid grid-cols-7 gap-1">
                <template x-for="blankday in blankdays" :key="'blank-'+blankday">
                    <div class="h-9 w-full"></div>
                </template>
                <template x-for="date in no_of_days" :key="'date-'+date">
                    <div class="relative">
                        <button 
                            type="button"
                            @click="selectDate(date)"
                            x-text="date"
                            class="h-9 w-full rounded-xl text-xs font-bold transition-all duration-200 relative z-10 flex items-center justify-center"
                            :class="{
                                'bg-linear-to-r from-brand-lila to-brand-purple text-white shadow-lg shadow-brand-purple/40 scale-105': isSelected(date),
                                'text-gray-700 dark:text-gray-200 hover:bg-brand-purple/15 hover:text-brand-purple': !isSelected(date),
                                'text-brand-purple border border-brand-purple/30': isToday(date) && !isSelected(date)
                            }"
                        ></button>
                        <div x-show="isSelected(date)" class="absolute inset-0 bg-brand-purple/30 blur-md rounded-xl z-0"></div>
                    </div>
                </template>
            </div>

            <!-- Footer con "Hoy" -->
            <div class="mt-4 pt-4 border-t border-gray-100 dark:border-white/5 flex justify-center">
                <button 
                    type="button" 
                    @click="let today = new Date(); month = today.getMonth(); year = today.getFullYear(); selectDate(today.getDate())"
                    class="text-[10px] font-black uppercase tracking-[0.2em] text-brand-lila hover:text-brand-neon transition-colors"
                >
                    Seleccionar Hoy
                </button>
            </div>
        </div>
        
        <div class="absolute inset-0 rounded-2xl border border-transparent group-hover:border-brand-purple/10 pointer-events-none transition-colors duration-300"></div>
    </div>
    <x-input-error :messages="$errors->get($name)" class="mt-1" />
</div>
