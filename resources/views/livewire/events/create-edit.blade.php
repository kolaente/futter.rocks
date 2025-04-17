<x-main-content :title="$event ? __('Edit :item', ['item' => $event->title]) : __('Create Event')">
    <x-floating-content class="p-4">
        <form wire:submit="store">
            {{ $this->form }}

            <x-button type="submit" class="mt-8">
                {{ __('Save Event') }}
            </x-button>
        </form>
    </x-floating-content>
</x-main-content>
