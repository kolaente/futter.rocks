<x-main-content :title="$recipe ? __('Edit :item', ['item' => $recipe->title]) : __('Create Recipe')">
    <x-floating-content class="p-4">
        <form wire:submit="store">
            {{ $this->form }}

            <x-button type="submit" class="mt-8">
                {{ __('Save Recipe') }}
            </x-button>
        </form>
    </x-floating-content>
</x-main-content>
