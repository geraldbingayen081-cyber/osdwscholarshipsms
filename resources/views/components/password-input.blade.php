@props([
    'name' => 'password',
    'id' => null,
    'placeholder' => '••••••••',
    'value' => null,
    'required' => false,
    'autocomplete' => 'current-password',
])

@php
    $inputId = $id ?? $name;
    $hasError = isset($errors) && $errors->has($name);
@endphp

<div x-data="{ show: false }" class="relative w-full">
    <input 
        :type="show ? 'text' : 'password'"
        name="{{ $name }}"
        id="{{ $inputId }}"
        @if($value !== null) value="{{ $value }}" @endif
        placeholder="{{ $placeholder }}"
        autocomplete="{{ $autocomplete }}"
        {{ $required ? 'required' : '' }}
        {{ $attributes->merge([
            'class' => 'w-full pr-10 rounded-lg border text-sm transition focus:ring-2 focus:outline-none font-medium ' . 
                ($hasError 
                    ? 'border-red-500 bg-red-50 dark:bg-red-950/40 text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:ring-red-500 focus:border-red-500' 
                    : 'border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:ring-[#6B0F1A] focus:border-[#6B0F1A] dark:focus:ring-amber-400 dark:focus:border-amber-400')
        ]) }}
    >
    <button 
        type="button" 
        @click="show = !show"
        tabindex="-1"
        class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 focus:outline-none cursor-pointer transition-colors"
        :aria-label="show ? 'Hide password' : 'Show password'"
        :title="show ? 'Hide password' : 'Show password'"
    >
        <!-- Closed Eye / Eye-off (Visible by default when password is hidden) -->
        <svg x-show="!show" class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
        </svg>
        <!-- Open Eye (Visible when password is shown/revealed) -->
        <svg x-show="show" x-cloak class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
        </svg>
    </button>
</div>
