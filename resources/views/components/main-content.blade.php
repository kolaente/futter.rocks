<div>
    <!-- Page Heading -->
    @if (isset($header) || isset($title) || isset($actions))
        <header class="bg-white dark:bg-gray-800 shadow">
            <div class="max-w-7xl mx-auto py-2 px-4 sm:px-6 lg:px-8 flex items-center justify-between">
                @if(isset($title))
                    <h2 class="font-display py-4 font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                        {{ $title }}
                    </h2>
                @endif

                @if(isset($header))
                    {{ $header }}
                @endif

                @if(isset($actions))
                    <div class="flex items-center space-x-2">
                        {{ $actions }}
                    </div>
                @endif
            </div>
        </header>
    @endif

    <!-- Page Content -->
    <main class="text-gray-800 dark:text-gray-200">
        {{ $slot }}
    </main>
</div>
