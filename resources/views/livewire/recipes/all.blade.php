<x-main-content :title="__('Recipes')">
    <x-slot:actions>
        <x-action-button href="{{ route('recipes.create') }}">
            {{ __('Create') }}
        </x-action-button>

        <livewire:recipes.create-from-text/>

        <livewire:recipes.import/>
    </x-slot:actions>

    <x-floating-content>
        {{ $this->table }}
    </x-floating-content>
</x-main-content>
