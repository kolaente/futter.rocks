<x-guest-layout :title="__('Privacy Policy')">
    <div class="pt-4 bg-gray-100">
        <div class="min-h-screen flex flex-col items-center pt-6 sm:pt-0">
            <div>
                <x-application-logo />
            </div>

            <div class="w-full sm:max-w-6xl mt-6 p-6 bg-white shadow-md overflow-hidden sm:rounded-lg prose prose-h1:font-display prose-h2:font-display prose-h3:font-display prose-h4:font-display prose-h5:font-display prose-h6:font-display">
                {!! $policy !!}
            </div>
        </div>
    </div>
</x-guest-layout>
