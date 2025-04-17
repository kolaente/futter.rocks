@php
    $tag = 'button';
    if($attributes->get('href', false) !== false) {
        $tag = 'a';
    }
@endphp

<{{ $tag }} {{ $tag === 'a' ? 'wire:navigate': '' }} {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-primary border border-transparent rounded-lg font-semibold text-sm text-white hover:bg-primary-dark focus:bg-gray-700 dark:focus:bg-white active:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-primary-light focus:ring-offset-2 disabled:opacity-50 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</{{ $tag }}>
