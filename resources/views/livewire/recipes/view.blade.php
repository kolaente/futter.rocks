<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-4">

            <x-button href="{{ route('recipes.edit', ['recipe' => $recipe]) }}">
                {{ __('Edit') }}
            </x-button>

            <table>
                <thead>
                <tr>
                    <th class="text-right p-1" colspan="2">{{ __('Quantity') }}</th>
                    <th class="text-left p-1">{{ __('Ingredient') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($recipe->ingredients as $ingredient)
                    <tr>
                        <td class="text-right py-1">{{ $ingredient->pivot->quantity }}</td>
                        <td class="text-left py-1">{{ $ingredient->pivot->unit->getShortLabel() }}</td>
                        <td class="text-left p-1">{{ $ingredient->title }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
