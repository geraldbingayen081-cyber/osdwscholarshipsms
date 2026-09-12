@props([
    'iconVariant' => 'mini',
    'size' => null,
])

@php
$attributes = $attributes->merge([
    'variant' => 'subtle',
    'class' => '-me-1',
    'square' => true,
    'size' => null,
]);
@endphp

<flux:button
    :$attributes
    :size="$size === 'sm' || $size === 'xs' ? 'xs' : 'sm'"
    x-data="fluxInputViewable"
    x-on:click="toggle()"
    x-bind:data-viewable-open="open"
    aria-label="{{ __('Toggle password visibility') }}"
    tabindex="-1"
    type="button"
>
    <flux:icon.eye :variant="$iconVariant" class="hidden [[data-viewable-open]>&]:block" />
    <flux:icon.eye-slash :variant="$iconVariant" class="block [[data-viewable-open]>&]:hidden" />
</flux:button>
