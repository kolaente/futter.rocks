@props(['event'])

<div class="max-w-7xl print:hidden mx-auto -mt-4">
    <a href="{{ route('events.view', ['event' => $event]) }}"
       class="text-sm font-semibold text-gray-600 ml-4 flex items-center justify-center sm:justify-start px-4 py-4 sm:py-0">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="size-4 mr-1">
            <path fill-rule="evenodd"
                  d="M14 8a.75.75 0 0 1-.75.75H4.56l3.22 3.22a.75.75 0 1 1-1.06 1.06l-4.5-4.5a.75.75 0 0 1 0-1.06l4.5-4.5a.75.75 0 0 1 1.06 1.06L4.56 7.25h8.69A.75.75 0 0 1 14 8Z"
                  clip-rule="evenodd"/>
        </svg>
        <span>
                {{ __('Back to Event') }}
            </span>
    </a>
</div>
