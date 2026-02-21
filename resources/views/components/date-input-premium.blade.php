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
    :class="show ? 'z-[60]' : 'z-0'"
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

        toggleCalendar(forceOpen = false) {
            if (!this.show || forceOpen) {
                const rect = this.$el.getBoundingClientRect();
                const windowHeight = window.innerHeight;
                const calendarHeight = 420;
                
                this.isUp = (windowHeight - rect.bottom) < calendarHeight && rect.top > (windowHeight - rect.bottom);
            }
            if(!forceOpen) {
                this.show = !this.show;
            }
        },

        formatDateForDisplay() {
            if (!this.selectedDate) {
                this.displayText = '';
                return;
            }
            let dateParts = this.selectedDate.split('-');
            if(dateParts.length === 3) {
                this.displayText = `${dateParts[2]}/${dateParts[1]}/${dateParts[0]}`;
            }
        },

        handleInput(e) {
            let val = e.target.value;
            if (e.inputType === 'insertText' || e.inputType === 'insertFromPaste') {
                if (/^\d{2}$/.test(val)) val += '/';
                if (/^\d{2}\/\d{2}$/.test(val)) val += '/';
            }
            this.displayText = val;
            
            let day, month, year;
            if (val.length === 8 && !val.includes('/') && !val.includes('-')) {
                day = parseInt(val.substring(0, 2));
                month = parseInt(val.substring(2, 4));
                year = parseInt(val.substring(4, 8));
            } else {
                let parts = val.split(/[\/\-]/);
                if (parts.length === 3 && parts[2].length === 4) {
                    day = parseInt(parts[0]);
                    month = parseInt(parts[1]);
                    year = parseInt(parts[2]);
                }
            }
            
            if (day && month && year) {
                let d = new Date(year, month - 1, day);
                if (d.getFullYear() === year && d.getMonth() === month - 1 && d.getDate() === day) {
                    this.selectedDate = year + '-' + String(month).padStart(2, '0') + '-' + String(day).padStart(2, '0');
                    this.month = month - 1;
                    this.year = year;
                    this.getNoOfDays();
                } else {
                    this.selectedDate = '';
                }
            } else {
                this.selectedDate = '';
            }
        },

        onBlur() {
            setTimeout(() => { 
                if(!this.$el.contains(document.activeElement)) {
                    if(this.selectedDate) {
                        this.formatDateForDisplay();
                    } else {
                        this.displayText = '';
                    }
                    this.show = false;
                }
            }, 100);
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

        <!-- Input oculto para validación nativa -->
        <input type="text" name="{{ $name }}" x-model="selectedDate" class="absolute w-0 h-0 opacity-0 pointer-events-none" tabindex="-1" @if($required) required @endif>

        <div class="w-full relative flex items-center">
            <input
                x-ref="dateInput"
                type="text"
                x-model="displayText"
                @input="handleInput($event)"
                @focus="show = true; toggleCalendar(true)"
                @blur="onBlur()"
                @keydown.escape="show = false; $refs.dateInput.blur()"
                @keydown.enter.prevent="show = false; onBlur()"
                class="w-full h-14 bg-white dark:bg-[#1a1a1a] border border-gray-200 dark:border-white/5 rounded-2xl text-sm text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand-purple/20 placeholder-gray-400 dark:placeholder-gray-500 transition-all duration-300 shadow-sm dark:shadow-none {{ $icon ? 'pl-11' : 'pl-4' }} pr-12"
                :class="show ? 'ring-2 ring-brand-purple/20 bg-gray-50 dark:bg-[#222]' : 'hover:bg-gray-50 dark:hover:bg-[#222]'"
                placeholder="{{ $placeholder }}"
                autocomplete="off">

            <button type="button" @click="show = !show; if(show) { toggleCalendar(true); $refs.dateInput.focus(); }" tabindex="-1" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 transition-transform duration-300 hover:text-brand-purple focus:outline-none cursor-pointer" :class="show && 'rotate-180 text-brand-purple'">
                <x-mary-icon name="o-chevron-down" class="w-4 h-4" />
            </button>
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
            style="display: none;">
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
                            }"></button>
                        <div x-show="isSelected(date)" class="absolute inset-0 bg-brand-purple/30 blur-md rounded-xl z-0"></div>
                    </div>
                </template>
            </div>

            <!-- Footer con "Hoy" -->
            <div class="mt-4 pt-4 border-t border-gray-100 dark:border-white/5 flex justify-center">
                <button
                    type="button"
                    @click="let today = new Date(); month = today.getMonth(); year = today.getFullYear(); selectDate(today.getDate())"
                    class="text-[10px] font-black uppercase tracking-[0.2em] text-brand-lila hover:text-brand-neon transition-colors">
                    Seleccionar Hoy
                </button>
            </div>
        </div>

        <div class="absolute inset-0 rounded-2xl border border-transparent group-hover:border-brand-purple/10 pointer-events-none transition-colors duration-300"></div>
    </div>
    <x-input-error :messages="$errors->get($name)" class="mt-1" />
</div>