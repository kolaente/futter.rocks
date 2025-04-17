<x-main-content :title="$group ? __('Edit :item', ['item' => $group->title]) : __('Create Group')">
    <x-floating-content class="p-4">
        <form wire:submit="store">
            {{ $this->form }}

            <x-button type="submit" class="mt-8">
                {{ __('Save Group') }}
            </x-button>
        </form>
    </x-floating-content>
</x-main-content>
