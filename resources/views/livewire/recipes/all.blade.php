<x-main-content :title="__('Recipes')">
    <x-slot:actions>
        <x-button href="{{ route('recipes.create') }}">
            {{ __('Create') }}
        </x-button>

        <livewire:recipes.import/>
    </x-slot:actions>

    <x-floating-content>
        {{ $this->table }}
    </x-floating-content>
</x-main-content>
