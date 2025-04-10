<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">

            <div class="p-4 flex justify-end space-x-2">
                <x-button href="{{ route('recipes.create') }}">
                    {{ __('Create Recipe') }}
                </x-button>

                <livewire:recipes.import/>
            </div>

            {{ $this->table }}
            <x-filament-actions::modals/>
        </div>
    </div>
</div>
