<x-main-content :title="__('Shopping list for :event', ['event' => $event->title])">
    <x-slot:actions>
        <a href="{{ route('events.view', ['event' => $event]) }}" class="text-sm font-semibold text-gray-600 mr-2 flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="size-4 mr-1">
                <path fill-rule="evenodd" d="M14 8a.75.75 0 0 1-.75.75H4.56l3.22 3.22a.75.75 0 1 1-1.06 1.06l-4.5-4.5a.75.75 0 0 1 0-1.06l4.5-4.5a.75.75 0 0 1 1.06 1.06L4.56 7.25h8.69A.75.75 0 0 1 14 8Z" clip-rule="evenodd" />
            </svg>
            <span>
                {{ __('Back to Event') }}
            </span>
        </a>
        <x-button onclick="window.print()">
            {{ __('Print') }}
        </x-button>
        <x-button wire:click="download" target="download">
            {{ __('Download') }}
        </x-button>
    </x-slot:actions>

    <x-floating-content class="p-4">
        @include('partials.shopping-list', ['event' => $event])
    </x-floating-content>
</x-main-content>
