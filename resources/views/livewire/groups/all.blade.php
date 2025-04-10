<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">

            <x-button href="{{ route('participant-groups.create') }}">
                {{ __('Create Group') }}
            </x-button>

            {{ $this->table }}
        </div>
    </div>
</div>
