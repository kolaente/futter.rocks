<x-main-content :title="__('Events')" :collapse-actions="false">
    <x-slot:actions>
        <x-button href="{{ route('events.create') }}">
            {{ __('Create') }}
        </x-button>
    </x-slot:actions>

    <x-floating-content>
        {{ $this->table }}
    </x-floating-content>
</x-main-content>
