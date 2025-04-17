<x-main-content :title="__('Groups')">
    <x-slot:actions>
        <x-button href="{{ route('participant-groups.create') }}">
            {{ __('Create') }}
        </x-button>
    </x-slot:actions>

    <x-floating-content>
        {{ $this->table }}
    </x-floating-content>
</x-main-content>
