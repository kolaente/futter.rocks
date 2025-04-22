<x-guest-layout>
    <div class="py-6 px-4 md:px-0 text-gray-800">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Top Nav Links --}}
            <div class="flex justify-between items-center mb-8">
                <x-application-logo class="mx-auto" />
                <div class="text-right">
                    @if (Route::has('login'))
                        <a href="{{ route('login') }}" class="font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-primary">{{ __('Log in') }}</a>
                    @endif

                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="ml-4 font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-primary">{{ __('Register') }}</a>
                    @endif
                </div>
            </div>

            <div class="p-6 text-gray-900 dark:text-gray-100 text-center min-h-[50vh] flex flex-col justify-center items-center">
                <h1 class="text-5xl font-bold mb-4 font-display">
                    {{ __('Camp Kitchen Planning made simple') }}
                </h1>

                <p class="mb-6">
                    {{ __('Focus on your event, not the spreadsheets.') }}
                </p>

                <x-button href="{{ route('dashboard') }}">
                    {{ __('Start Planning now') }}
                </x-button>
            </div>

            {{-- Features Section --}}
            <div class="mt-16 space-y-12 md:space-y-24">
                <x-feature-section
                    :title="__('Meal Planning')"
                    :features="[
                        __('Create clear meal plans for your camp or retreat.'),
                        __('Assign specific recipes to each day and meal.'),
                        __('Keep track of all planned dishes.'),
                    ]"
                    image-order="right"
                />
                <x-feature-section
                    :title="__('Groups & Food Factor')"
                    :features="[
                        __('Define participant groups and their numbers.'),
                        __('Adjust the food factor to scale quantities for each group.'),
                        __('Automatic quantity calculation based on recipes and participant numbers.'),
                    ]"
                    image-order="left"
                />
                <x-feature-section
                    :title="__('Generate Shopping List')"
                    :features="[
                        __('Create one or multiple shopping lists for your event'),
                        __('Combines identical ingredients from different recipes'),
                        __('Set the dates when you need to go shopping, we\'ll do the rest'),
                    ]"
                    image-order="right"
                />
                <x-feature-section
                    :title="__('Manage Recipes')"
                    :features="[
                        __('Save and organize your tried-and-tested group recipes.'),
                        __('Create a central recipe database for your group.'),
                        __('Import recipes quickly and easily from various sources (e.g. from Chefkoch).'),
                    ]"
                    image-order="left"
                />
                <x-feature-section
                    :title="__('Collaboration')"
                    :features="[
                        __('Work together with your group on meal plans and recipes.'),
                    ]"
                    image-order="right"
                />
                <x-feature-section
                    :title="__('Campflow Import')"
                    :features="[
                        __('Import participant data directly from Campflow.'),
                    ]"
                    image-order="left"
                    :coming-soon="true"
                />
                <x-feature-section
                    :title="__('Open Source')"
                    :features="[
                        __('Futter.rocks is open source under the AGPLv3 license.'),
                        __('The code is available on GitHub and can be viewed and adapted.'),
                        __('Benefit from the community and contribute yourself.'),
                    ]"
                    image-order="right"
                />
            </div>

            {{-- Bottom CTA --}}
            <div class="my-36 text-center">
                <p class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">
                    {{ __('Ready to simplify your kitchen planning?') }}
                </p>
                <x-button href="{{ route('dashboard') }}">
                    {{ __('Start Planning now') }}
                </x-button>
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
