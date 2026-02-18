@props([
    'label' => null,
    'name',
    'type' => 'text',
    'icon' => null,
    'placeholder' => '',
    'required' => false,
    'value' => '',
])

<div class="space-y-2" x-data="{ show: false }">
    @if($label)
        <label for="{{ $name }}" class="text-[10px] font-bold text-gray-500 dark:text-gray-300 uppercase tracking-[0.2em] ml-1">
            {{ $label }} @if($required)<span class="text-brand-purple">*</span>@endif
        </label>
    @endif
    
    <div class="relative group">
        @if($icon)
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                <x-mary-icon :name="$icon" class="w-5 h-5 text-gray-400 dark:text-gray-500 group-focus-within:text-brand-purple transition-colors duration-300" />
            </div>
        @endif

        <input 
            :type="'{{ $type }}' === 'password' && show ? 'text' : '{{ $type }}'"
            name="{{ $name }}" 
            id="{{ $name }}" 
            value="{{ old($name, $value) }}" 
            placeholder="{{ $placeholder }}" 
            @if($required) required @endif
            {{ $attributes->merge(['class' => 'block w-full ' . ($icon ? 'pl-11' : 'pl-4') . ' ' . ($type === 'password' ? 'pr-12' : 'pr-4') . ' py-4 h-14 bg-white dark:bg-[#1a1a1a] border-gray-200 dark:border-none rounded-2xl text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:bg-gray-50 dark:focus:bg-[#222] focus:ring-2 focus:ring-brand-purple/20 transition-all duration-300 shadow-sm dark:shadow-none appearance-none']) }}
        />

        @if($type === 'password')
            <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 pr-4 flex items-center cursor-pointer text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-white transition-colors focus:outline-none">
                <x-mary-icon x-show="!show" name="o-eye" class="w-5 h-5" />
                <x-mary-icon x-show="show" name="o-eye-slash" class="w-5 h-5" />
            </button>
        @endif
    </div>
    <x-input-error :messages="$errors->get($name)" class="mt-1" />
</div>
