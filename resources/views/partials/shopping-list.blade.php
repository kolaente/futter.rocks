@php
    $fmt = new \App\Formatter();
    $list = $event->getShoppingList();
    $shoppingToursById = $event->shoppingTours->keyBy('id');
@endphp

@foreach($list as $shoppingTourId => $tourListByCategories)

    @if(count($list) > 1)
        <h2 class="shopping-tour-list font-display font-semibold text-2xl mb-2 mt-6">
            @if($shoppingTourId === 0)
                {{ __('Before the event') }}:
            @else
                {{ __('At :date', ['date' => $shoppingToursById->get($shoppingTourId)->date->translatedFormat(__('j F Y'))]) }}:
            @endif
        </h2>
    @endif

    @foreach($tourListByCategories as $category => $tourList)
        <div>
            <h3 class="shopping-tour-list font-display font-semibold text-lg mb-0 mt-4">
                {{ \App\Models\Enums\IngredientCategory::from($category)->getLabel() }}
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                @foreach(collect($tourList)->splitIn(2) as $items)
                    <ul>
                        @foreach($items as $item)
                            <li>
                                <span class="check inline-block w-3 h-3 border border-gray-500"></span>
                                {{ $fmt->format($item['quantity']) }} {{ $item['unit']->getShortLabel() }} {{ $item['ingredient']->title }}
                            </li>
                        @endforeach
                    </ul>
                @endforeach
            </div>
        </div>
    @endforeach
@endforeach
