<x-app-layout :title="__('Dashboard')">
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-3 gap-6">

            <div class="md:col-span-2 space-y-6">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6">
                        <h2 class="font-display text-xl font-semibold text-gray-800 dark:text-gray-200">
                            {{ isset($currentEvent) ? __('Next Event: :title', ['title' => $currentEvent->title]) : __('No Active Event') }}
                        </h2>
                        @if(isset($currentEvent))
                            <p class="text-gray-600 dark:text-gray-400 mb-2">
                                {{ $currentEvent->durationString }}
                            </p>
                            <div class="flex flex-wrap gap-4 mt-4">
                                <div class="bg-gray-100 dark:bg-gray-700 rounded-lg p-3 flex-1">
                                    <span class="block text-sm text-gray-500 dark:text-gray-400">{{ __('Participants') }}</span>
                                    <span class="text-xl font-bold text-gray-800 dark:text-gray-200">
                                        {{ $participantCount ?? __('N/A') }}
                                    </span>
                                </div>
                                <div class="bg-gray-100 dark:bg-gray-700 rounded-lg p-3 flex-1">
                                    <span class="block text-sm text-gray-500 dark:text-gray-400">{{ __('Recipes') }}</span>
                                    <span class="text-xl font-bold text-gray-800 dark:text-gray-200">
                                        {{ $recipeCount }}
                                    </span>
                                </div>
                                <div class="bg-gray-100 dark:bg-gray-700 rounded-lg p-3 flex-1">
                                    <span class="block text-sm text-gray-500 dark:text-gray-400">{{ __('Meals') }}</span>
                                    <span class="text-xl font-bold text-gray-800 dark:text-gray-200">
                                        {{ $currentEvent->meals->count() }}
                                    </span>
                                </div>
                            </div>
                        @else
                            <p class="text-gray-600 dark:text-gray-400 mt-2">
                                {{ __('Select or create an event to begin planning.') }}
                            </p>
                            <div class="mt-4">
                                <x-button href="{{ route('events.create') }}" wire:navigate>
                                    {{ __('Create Event') }}
                                </x-button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Column 2: Quick Actions & Links -->
            <div class="space-y-6">
                <!-- Quick Actions -->
                 <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-display font-semibold text-gray-800 dark:text-gray-200 mb-4">{{ __('Quick Actions') }}</h3>
                        <div class="space-y-3 flex flex-col">
                            <x-button variant="secondary" href="{{ route('recipes.create') }}">
                                {{ __('Create Recipe') }}
                            </x-button>
                            @if(isset($currentEvent))
                                <x-button variant="secondary" href="{{ route('events.view', $currentEvent) }}">
                                    {{ __('View Event') }}
                                </x-button>
                                <x-button variant="secondary" href="{{ route('events.meal-plan', $currentEvent) }}">
                                    {{ __('View Meal Plan') }}
                                </x-button>
                                <x-button variant="secondary" href="{{ route('events.shopping-list', $currentEvent) }}">
                                    {{ __('View Shopping List') }}
                                </x-button>
                             @endif
                        </div>
                    </div>
                </div>

                 <!-- Recent Recipes (Optional) -->
                 <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-display font-semibold text-gray-800 dark:text-gray-200 mb-2">
                            {{ __('Recent Recipes') }}
                        </h3>
                        @if($recentRecipes?->count() > 0)
                            <ul>
                                @foreach($recentRecipes as $recipe)
                                    <li>
                                        <a href="{{ route('recipes.view', $recipe) }}" class="text-primary hover:underline text-sm">
                                            {{ $recipe->title }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                             <p class="text-gray-600 dark:text-gray-400">{{ __('No recent recipes found.') }}</p>
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
