<x-main-content :title="$event->title . ' - ' . $event->duration_string">
    <x-slot:actions>
        <x-button href="{{ route('events.edit', ['event' => $event]) }}">
            {{ __('Edit') }}
        </x-button>
        <x-button href="{{ route('event.meal-plan-view', ['event' => $event]) }}">
            {{ __('Meal Plan') }}
        </x-button>
        <x-button href="{{ route('event.shopping-list-view', ['event' => $event]) }}">
            {{ __('Shopping list') }}
        </x-button>
        <x-button href="{{ route('shared.event.meal-plan', ['event' => $event]) }}">
            {{ __('Share') }}
        </x-button>
    </x-slot:actions>

    <x-floating-content>


        @if($event->description)
            <p class="p-4 mb-2">
                {{ $event->description }}
            </p>
        @endif

        <h2 class="font-display font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Groups') }}
        </h2>

        <livewire:events.list-groups :$event/>

        <h2 class="font-display font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Meals') }}
        </h2>

        <livewire:events.list-meals :$event/>

        <h2 class="font-display font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Shopping Tours') }}
        </h2>

        <livewire:events.list-shopping-tours :$event/>
    </x-floating-content>
</x-main-content>
