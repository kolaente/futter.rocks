<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">

            <div class="flex justify-end p-4">
                <x-button href="{{ route('events.edit', ['event' => $event]) }}">
                    {{ __('Edit') }}
                </x-button>
                // einkaufsliste
                // essensplan
                // share
            </div>

            @if($event->description)
                <p class="p-4 mb-2">
                    {{ $event->description }}
                </p>
            @endif

            <p class="p-4 mb-2">
                {{ __('From :from to :to', ['from' => $event->date_from, 'to' => $event->date_to]) }}
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
