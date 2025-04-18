<x-main-content :title="$event->title . ' - ' . $event->duration_string">
    <x-slot:actions>
        <x-button href="{{ route('events.edit', ['event' => $event]) }}">
            {{ __('Edit') }}
        </x-button>
        <x-button href="{{ route('events.meal-plan', ['event' => $event]) }}">
            {{ __('Meal Plan') }}
        </x-button>
        <x-button href="{{ route('events.shopping-list', ['event' => $event]) }}">
            {{ __('Shopping list') }}
        </x-button>
        {{ $this->deleteAction }}
    </x-slot:actions>

    <x-filament-actions::modals />

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if($event->description)
                <p class="p-4 mb-2 bg-white overflow-hidden ring-1 ring-gray-950/5 sm:rounded-xl mb-8">
                    {{ $event->description }}
                </p>
            @endif

            <h2 class="font-display font-semibold text-2xl text-gray-800 dark:text-gray-200 leading-tight mb-4">
                {{ __('Meals') }}
            </h2>

            <livewire:events.list-meals :$event/>

            <h2 class="font-display font-semibold text-2xl text-gray-800 dark:text-gray-200 leading-tight mb-4">
                {{ __('Groups') }}
            </h2>

            <livewire:events.list-groups :$event class="mb-6"/>

            <h2 class="font-display font-semibold text-2xl text-gray-800 dark:text-gray-200 leading-tight mb-4">
                {{ __('Shopping Tours') }}
            </h2>

            <livewire:events.list-shopping-tours :$event/>
        </div>
    </div>
</x-main-content>
