<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
            <form wire:submit="store">
                {{ $this->form }}

                <x-button type="submit" class="mt-8">
                    {{ __('Save Recipe') }}
                </x-button>
            </form>
        </div>
    </div>
</div>
