<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
            {{ $this->form }}

            <x-button wire:click="create" class="mt-8">
                {{ __('Create Event') }}
            </x-button>
        </div>
    </div>
</div>
