@props(['question'])

<div>
    <h3 class="text-xl font-semibold font-display">
        {{ $question }}
    </h3>
    <p class="mt-2 text-gray-600">
        {{ $slot }}
    </p>
</div>
