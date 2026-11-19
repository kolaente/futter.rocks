<x-main-content :title="$event ? __('Edit :item', ['item' => $event->title]) : __('Create Event')">
    <x-floating-content class="p-4">
        @if ($isEventInProgress)
            <p role="alert" class="mb-4 rounded-md border border-yellow-300 bg-yellow-50 p-3 text-sm text-yellow-900 dark:border-yellow-700 dark:bg-yellow-950 dark:text-yellow-100">
                {{ __('This event is in progress. Changes may make existing calculations inaccurate.') }}
            </p>
        @endif

        <form wire:submit="store">
            {{ $this->form }}

            <x-button type="submit" class="mt-8">
                {{ __('Save Event') }}
            </x-button>
        </form>
    </x-floating-content>
</x-main-content>
