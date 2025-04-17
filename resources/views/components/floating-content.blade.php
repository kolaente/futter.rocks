<div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div {{ $attributes->merge(['class' => 'bg-white overflow-hidden shadow-xl sm:rounded-lg']) }}>
            {{ $slot }}
        </div>
    </div>
</div>
