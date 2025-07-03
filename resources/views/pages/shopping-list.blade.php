<x-guest-layout title="{{ __('Shopping list') }}">
    <h1 class="font-display text-center text-4xl mt-4 mb-8 font-semibold">
        {{ $event->title }} - {{ __('Shopping list') }}
    </h1>

    @include('partials.shopping-list', ['event' => $event])
</x-guest-layout>
