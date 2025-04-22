<x-guest-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Top Nav Links --}}
            <div class="flex justify-between items-center mb-8">
                <x-application-logo class="mx-auto" />
                <div class="text-right">
                    @if (Route::has('login'))
                        <a href="{{ route('login') }}" class="font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500">{{ __('Log in') }}</a>
                    @endif

                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="ml-4 font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500">{{ __('Register') }}</a>
                    @endif
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100 text-center">
                    <h1 class="text-5xl font-bold mb-4 font-display">{{ __('Futter.rocks: Camp Kitchen Planning') }}</h1>

                    <p class="mb-4">
                        {{ __('Organizing food for camps or large groups? Futter.rocks helps you:') }}
                    </p>

                    <ul class="list-disc list-inside mb-4 mx-auto text-left" style="max-width: 400px;">
                        <li>{{ __('Plan meals for your event.') }}</li>
                        <li>{{ __('Automatically calculate ingredient amounts based on participants.') }}</li>
                        <li>{{ __('Keep your group recipes in one place.') }}</li>
                        <li>{{ __('Generate consolidated shopping lists.') }}</li>
                    </ul>

                    <p class="mb-6">
                        {{ __('Focus on your event, not the spreadsheets.') }}
                    </p>

                    @if (Route::has('register'))
                        <x-button href="{{ route('register') }}">
                            {{ __('Sign up free & Start Planning') }}
                        </x-button>
                    @endif
                </div>
            </div>

            {{-- Features Section --}}
            <div class="mt-16 space-y-16">
                {{-- Feature 1: Meal Planning --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
                    <div class="order-2 md:order-1 text-gray-900 dark:text-gray-100">
                        <h2 class="text-2xl font-bold font-display mb-4">{{ __('Meal Planning') }}</h2>
                        <ul class="list-disc list-inside space-y-2">
                            <li>{{ __('Create clear meal plans for your camp or retreat.') }}</li>
                            <li>{{ __('Assign specific recipes to each day and meal.') }}</li>
                            <li>{{ __('Keep track of all planned dishes.') }}</li>
                        </ul>
                    </div>
                    <div class="order-1 md:order-2 bg-gray-200 dark:bg-gray-700 h-64 rounded-lg flex items-center justify-center text-gray-500">
                        {{ __('[Screenshot Placeholder: Meal Plan]') }}
                    </div>
                </div>

                {{-- Feature 2: Groups & Food Factor --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
                    <div class="order-1 md:order-2 text-gray-900 dark:text-gray-100">
                        <h2 class="text-2xl font-bold font-display mb-4">{{ __('Groups & Food Factor') }}</h2>
                        <ul class="list-disc list-inside space-y-2">
                            <li>{{ __('Define participant groups and their numbers.') }}</li>
                            <li>{{ __('Adjust the "food factor" to scale quantities for hearty eaters or special needs.') }}</li>
                            <li>{{ __('Automatic quantity calculation based on recipes and participant numbers.') }}</li>
                        </ul>
                    </div>
                    <div class="order-2 md:order-1 bg-gray-200 dark:bg-gray-700 h-64 rounded-lg flex items-center justify-center text-gray-500">
                        {{ __('[Screenshot Placeholder: Group Calculation]') }}
                    </div>
                </div>

                {{-- Feature 3: Shopping List --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
                    <div class="order-2 md:order-1 text-gray-900 dark:text-gray-100">
                        <h2 class="text-2xl font-bold font-display mb-4">{{ __('Generate Shopping List') }}</h2>
                        <ul class="list-disc list-inside space-y-2">
                            <li>{{ __('Creates a consolidated shopping list for the entire meal plan.') }}</li>
                            <li>{{ __('Combines identical ingredients from different recipes.') }}</li>
                            <li>{{ __('Significantly simplifies the shopping process.') }}</li>
                        </ul>
                    </div>
                    <div class="order-1 md:order-2 bg-gray-200 dark:bg-gray-700 h-64 rounded-lg flex items-center justify-center text-gray-500">
                        {{ __('[Screenshot Placeholder: Shopping List]') }}
                    </div>
                </div>

                {{-- Feature 4: Recipes --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
                    <div class="order-1 md:order-2 text-gray-900 dark:text-gray-100">
                        <h2 class="text-2xl font-bold font-display mb-4">{{ __('Manage Recipes') }}</h2>
                        <ul class="list-disc list-inside space-y-2">
                            <li>{{ __('Save and organize your tried-and-tested group recipes.') }}</li>
                            <li>{{ __('Add ingredients, instructions, and portion sizes.') }}</li>
                            <li>{{ __('Create a central recipe database for your team.') }}</li>
                        </ul>
                    </div>
                    <div class="order-2 md:order-1 bg-gray-200 dark:bg-gray-700 h-64 rounded-lg flex items-center justify-center text-gray-500">
                        {{ __('[Screenshot Placeholder: Recipe Management]') }}
                    </div>
                </div>

                 {{-- Feature 5: Recipe Import --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
                    <div class="order-2 md:order-1 text-gray-900 dark:text-gray-100">
                        <h2 class="text-2xl font-bold font-display mb-4">{{ __('Recipe Import') }}</h2>
                        <ul class="list-disc list-inside space-y-2">
                            <li>{{ __('Import recipes quickly and easily from various sources (e.g., copy & paste from Chefkoch).') }}</li>
                            <li>{{ __('Save time when entering new recipes.') }}</li>
                            <li>{{ __('Effortlessly expand your recipe database.') }}</li>
                        </ul>
                    </div>
                    <div class="order-1 md:order-2 bg-gray-200 dark:bg-gray-700 h-64 rounded-lg flex items-center justify-center text-gray-500">
                        {{ __('[Screenshot Placeholder: Recipe Import]') }}
                    </div>
                </div>

                {{-- Feature 6: Team Collaboration --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
                    <div class="order-1 md:order-2 text-gray-900 dark:text-gray-100">
                        <h2 class="text-2xl font-bold font-display mb-4">{{ __('Collaboration') }}</h2>
                        <ul class="list-disc list-inside space-y-2">
                            <li>{{ __('Work together with your kitchen team on meal plans and recipes.') }}</li>
                            <li>{{ __('Share information and tasks efficiently.') }}</li>
                            <li>{{ __('Improve coordination and organization within the team.') }}</li>
                        </ul>
                    </div>
                    <div class="order-2 md:order-1 bg-gray-200 dark:bg-gray-700 h-64 rounded-lg flex items-center justify-center text-gray-500">
                        {{ __('[Screenshot Placeholder: Team Collaboration]') }}
                    </div>
                </div>

                {{-- Feature 7: Campflow Import (Coming Soon) --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
                    <div class="order-2 md:order-1 text-gray-900 dark:text-gray-100">
                        <h2 class="text-2xl font-bold font-display mb-4">{{ __('Campflow Import') }} <span class="text-sm text-gray-500">({{ __('(Coming Soon)') }})</span></h2>
                        <ul class="list-disc list-inside space-y-2">
                            <li>{{ __('Import participant data directly from Campflow.') }}</li>
                            <li>{{ __('Simplify the transfer of group information.') }}</li>
                            <li>{{ __('Seamless integration for Campflow users.') }}</li>
                        </ul>
                    </div>
                    <div class="order-1 md:order-2 bg-gray-200 dark:bg-gray-700 h-64 rounded-lg flex items-center justify-center text-gray-500">
                        {{ __('[Screenshot Placeholder: Campflow Import]') }}
                    </div>
                </div>

                {{-- Feature 8: Open Source --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
                    <div class="order-1 md:order-2 text-gray-900 dark:text-gray-100">
                        <h2 class="text-2xl font-bold font-display mb-4">{{ __('Open Source') }}</h2>
                        <ul class="list-disc list-inside space-y-2">
                            <li>{{ __('Futter.rocks is open source under the AGPLv3 license.') }}</li>
                            <li>{{ __('The code is available on GitHub and can be viewed and adapted.') }}</li>
                            <li>{{ __('Benefit from the community and contribute yourself.') }}</li>
                        </ul>
                    </div>
                    <div class="order-2 md:order-1 bg-gray-200 dark:bg-gray-700 h-64 rounded-lg flex items-center justify-center text-gray-500">
                        {{ __('[Icon/Logo Placeholder: Open Source / GitHub]') }}
                    </div>
                </div>

            </div>

            {{-- Bottom CTA --}}
            <div class="mt-16 text-center">
                <p class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">
                    {{ __('Ready to simplify your kitchen planning?') }}
                </p>
                @if (Route::has('register'))
                    <x-button href="{{ route('register') }}">
                        {{ __('Sign up for free now') }}
                    </x-button>
                @endif
            </div>

            {{-- FAQ Section --}}
            <div class="mt-16 pt-12 border-t border-gray-200 dark:border-gray-700">
                <h2 class="text-center text-3xl font-bold font-display mb-8 text-gray-900 dark:text-gray-100">{{ __('Frequently Asked Questions (FAQ)') }}</h2>
                <div class="max-w-3xl mx-auto space-y-6 text-gray-900 dark:text-gray-100">
                    <div>
                        <h3 class="text-xl font-semibold font-display">{{ __('Who is Futter.rocks for?') }}</h3>
                        <p class="mt-2 text-gray-600 dark:text-gray-400">{{ __('[Answer coming soon...]') }}</p>
                    </div>
                    <div>
                        <h3 class="text-xl font-semibold font-display">{{ __('Is it suitable for very large groups too?') }}</h3>
                        <p class="mt-2 text-gray-600 dark:text-gray-400">{{ __('[Answer coming soon...]') }}</p>
                    </div>
                    <div>
                        <h3 class="text-xl font-semibold font-display">{{ __('What happens to my data (Privacy)?') }}</h3>
                        <p class="mt-2 text-gray-600 dark:text-gray-400">{{ __('[Answer coming soon...]') }}</p>
                    </div>
                    <div>
                        <h3 class="text-xl font-semibold font-display">{{ __('Does using Futter.rocks cost anything?') }}</h3>
                        <p class="mt-2 text-gray-600 dark:text-gray-400">{{ __('[Answer coming soon...]') }}</p>
                    </div>
                    <div>
                        <h3 class="text-xl font-semibold font-display">{{ __('How can I collaborate with people from my group?') }}</h3>
                        <p class="mt-2 text-gray-600 dark:text-gray-400">{{ __('[Answer coming soon...]') }}</p>
                    </div>
                    <div>
                        <h3 class="text-xl font-semibold font-display">{{ __('I have a question or a problem, where can I get help?') }}</h3>
                        <p class="mt-2 text-gray-600 dark:text-gray-400">{{ __('[Answer coming soon...]') }}</p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-guest-layout>
