@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center rounded px-4 py-2 bg-primary text-white text-sm font-semibold focus:outline-none focus:ring-primary-dark transition duration-150 ease-in-out'
            : 'inline-flex items-center rounded px-4 py-2 bg-transparent hover:bg-primary-light text-gray-500 hover:text-white text-sm font-semibold focus:outline-none focus:ring-primary-dark transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }} wire:navigate>
    {{ $slot }}
</a>
