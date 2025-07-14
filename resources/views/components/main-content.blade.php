@props([
    'header' => null,
    'title' => null,
    'collapseActions' => true,
])

<div>
    <!-- Page Heading -->
    @if (isset($header) || isset($title) || isset($actions))
        <header>
            <div class="max-w-7xl print:max-w-full mx-auto py-2 px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-start sm:items-center justify-between space-y-2 sm:space-y-0 print:px-4">
                @if(isset($title))
                    <h2 class="font-display py-4 font-semibold text-3xl text-gray-800 dark:text-gray-200 leading-tight">
                        {{ $title }}
                    </h2>
                @endif

                @if(isset($header))
                    {{ $header }}
                @endif

                @if(isset($actions))
                    @if($collapseActions)
                        <div
                            class="collapsable-action-buttons print:hidden pl-2 w-full sm:w-auto"
                            x-data="{ open: false, isDesktop: false }"
                            x-init="isDesktop = window.matchMedia('(min-width: 1024px)').matches; window.matchMedia('(min-width: 1024px)').addEventListener('change', (e) => isDesktop = e.matches)"
                            x-cloak
                        >
                            <!-- Actions rendered only once with responsive positioning -->
                            <div class="lg:flex lg:flex-row lg:items-center lg:space-x-2 lg:static lg:w-auto lg:bg-transparent lg:border-0 lg:shadow-none lg:ring-0 lg:py-0 lg:mt-0 lg:space-y-0 lg:rounded-none
                                        mt-12 absolute w-auto md:w-56 left-2 md:left-auto right-2 z-10 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none dark:bg-gray-800 dark:ring-gray-700 py-1 space-y-1 flex flex-col"
                                 x-show="open || isDesktop"
                                 @click.away="open = false"
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="transform opacity-0 scale-95"
                                 x-transition:enter-end="transform opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="transform opacity-100 scale-100"
                                 x-transition:leave-end="transform opacity-0 scale-95">
                                {{ $actions }}
                            </div>

                            <!-- Mobile: Dropdown trigger button -->
                            <div class="lg:hidden relative w-full md:w-auto collapsable-action-buttons">
                                <button @click="open = !open" class="flex items-center justify-center w-full sm:w-auto px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700">
                                    {{ __('Actions') }}
                                    <svg class="w-4 h-4 ml-2" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    @else
                        <div class="flex items-center space-x-2 print:hidden">
                            {{ $actions }}
                        </div>
                    @endif
                @endif
            </div>
        </header>
    @endif

    <!-- Page Content -->
    <main class="text-gray-800 dark:text-gray-200">
        {{ $slot }}
    </main>
</div>
