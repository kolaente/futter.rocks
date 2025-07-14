<x-main-content :title="__('Meal plan for :event', ['event' => $event->title])">
    <x-slot:actions>
        <x-action-button onclick="window.print()">
            {{ __('Print') }}
        </x-action-button>
        <x-action-button wire:click="download" target="download">
            {{ __('Download') }}
        </x-action-button>
        <x-action-button href="{{ route('shared.event.meal-plan', ['shareId' => $event->share_id]) }}">
            {{ __('Share') }}
        </x-action-button>
    </x-slot:actions>

    <x-back-to-event :$event/>

    <x-floating-content class="p-3 sm:p-4">
        @include('partials.meal-plan', ['mealsByDate' => $this->mealsByDate])
    </x-floating-content>
</x-main-content>
