<x-main-content :title="__('Shopping list for :event', ['event' => $event->title])">
    <x-slot:actions>
        <x-action-button onclick="window.print()">
            {{ __('Print') }}
        </x-action-button>
        <x-action-button wire:click="download" target="download">
            {{ __('Download') }}
        </x-action-button>
    </x-slot:actions>

    <x-back-to-event :$event/>

    <x-floating-content class="p-4">
        @include('partials.shopping-list', ['event' => $event])
    </x-floating-content>
</x-main-content>
