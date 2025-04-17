<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">

            <div class="flex justify-end p-4 space-x-2">
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
            </div>

            @if($event->description)
                <p class="p-4 mb-2">
                    {{ $event->description }}
                </p>
            @endif

            <p class="p-4 mb-2">
                {{ __('From :from to :to, :days.', [
                    'from' => $event->date_from->translatedFormat('j F Y'),
                    'to' => $event->date_to->translatedFormat('j F Y'),
                    'days' => trans_choice(':count day|:count days', $event->duration_days),
                ]) }}
            </p>

            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Groups') }}
            </h2>

            <livewire:events.list-groups :$event/>

            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Meals') }}
            </h2>

            <livewire:events.list-meals :$event/>

            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Shopping Tours') }}
            </h2>

            <livewire:events.list-shopping-tours :$event/>
        </div>
    </div>
</div>
