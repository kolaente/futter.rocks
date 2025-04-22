@props([
    'title',
    'features' => [],
    'imagePath' => null,
    'imageOrder' => 'right', // 'left' or 'right' determines image position on desktop
    'comingSoon' => false
])

<div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
    <div @class([
        'order-1 md:order-1' => $imageOrder === 'right',
        'order-1 md:order-2' => $imageOrder === 'left',
    ])>
        <h2 class="text-2xl font-bold font-display mb-4">
            {{ $title }}
            @if($comingSoon)
                <span class="text-sm text-gray-500">({{ __('Coming Soon') }})</span>
            @endif
        </h2>
        <ul class="list-disc space-y-2 ml-6">
            @foreach ($features as $feature)
                <li>{{ $feature }}</li>
            @endforeach
        </ul>
    </div>
    <div @class([
        'order-2 md:order-2' => $imageOrder === 'right',
        'order-2 md:order-1' => $imageOrder === 'left',
        'h-64 rounded-lg',
        'bg-gray-200 dark:bg-gray-700 flex items-center justify-center text-gray-500' => !$imagePath
    ])>
        @if($imagePath)
            <img src="{{ asset($imagePath) }}" alt="{{ __($title) }} feature illustration" class="object-cover w-full h-full rounded-lg">
        @else
        Placeholder
        @endif
    </div>
</div>
