<div class="py-6 print:py-0">
    <div class="max-w-7xl print:max-w-full mx-auto px-4 sm:px-6 lg:px-8 print:px-0">
        <div {{ $attributes->merge(['class' => 'bg-white overflow-hidden shadow-xl sm:rounded-lg print:rounded-none print:shadow-none']) }}>
            {{ $slot }}
        </div>
    </div>
</div>
