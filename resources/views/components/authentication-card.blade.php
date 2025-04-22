<div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100 dark:bg-gray-900">
    <div>
        {{ $logo }}
    </div>

    <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white dark:bg-gray-800 shadow-md overflow-hidden sm:rounded-lg">
        {{ $slot }}
    </div>

    <div class="text-gray-600 text-sm mt-4 flex space-x-2">
        <a href="{{ route('imprint') }}" class="hover:text-gray-900">{{ __('Imprint') }}</a>
        <span class="text-gray-400">&middot;</span>
        <a href="{{ route('policy.show') }}" class="hover:text-gray-900">{{ __('Privacy Policy') }}</a>
    </div>
</div>
