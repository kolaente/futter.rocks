@props(['target' => null, 'variant' => 'primary'])

@php
    $mobileClasses = 'lg:rounded-lg lg:shadow-sm lg:border-transparent w-full lg:w-auto justify-start lg:justify-center !shadow-none text-left lg:text-center rounded-none border-0 shadow-none px-3 py-2 lg:px-4 lg:py-2';

    if ($variant === 'primary') {
        $mobileClasses .= ' !bg-white lg:!bg-primary !text-gray-900 lg:!text-white hover:!bg-gray-50 dark:hover:!bg-gray-700 lg:hover:!bg-primary-dark';
    }

    if ($variant === 'secondary') {
        $mobileClasses .= ' hover:!bg-gray-50';
    }
@endphp

<x-button
    :target="$target"
    :variant="$variant"
    {{ $attributes->merge(['class' => $mobileClasses]) }}
>
    {{ $slot }}
</x-button>
