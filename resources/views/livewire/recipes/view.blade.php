@php($fmt = new \App\Formatter())

<x-main-content :title="$recipe->title">
    <x-slot:actions>
        <x-button href="{{ route('recipes.edit', ['recipe' => $recipe]) }}">
            {{ __('Edit') }}
        </x-button>
        {{ $this->deleteAction }}
    </x-slot:actions>

    <x-filament-actions::modals />

    <x-floating-content class="p-2">
        <table>
            <thead>
            <tr>
                <th class="text-right p-1" colspan="2">{{ __('Quantity') }}</th>
                <th class="text-left p-1">{{ __('Ingredient') }}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($recipe->ingredients->sortBy('title') as $ingredient)
                <tr>
                    <td class="text-right py-1">{{ $fmt->format($ingredient->pivot->quantity) }}</td>
                    <td class="text-left py-1">{{ $ingredient->pivot->unit->getShortLabel() }}</td>
                    <td class="text-left p-1">{{ $ingredient->title }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </x-floating-content>
</x-main-content>
