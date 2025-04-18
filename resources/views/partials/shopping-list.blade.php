@php
    $fmt = new NumberFormatter('de_DE', NumberFormatter::DECIMAL);
    $list = $event->getShoppingList();
    $shoppingToursById = $event->shoppingTours->keyBy('id');
@endphp

@foreach($list as $shoppingTourId => $tourList)

    @if(count($list) > 1)
        <h2 class="shopping-tour-list font-display font-semibold text-xl mb-2 mt-4">
            @if($shoppingTourId === 0)
                {{ __('Before the event') }}:
            @else
                {{ __('At :date', ['date' => $shoppingToursById->get($shoppingTourId)->date]) }}:
            @endif
        </h2>
    @endif

    <ul>
        @foreach($tourList as $item)
            <li>
                <span class="check inline-block w-3 h-3 border border-gray-500"></span>
                {{ $fmt->format($item['quantity']) }} {{ $item['unit']->getShortLabel() }} {{ $item['ingredient']->title }}
            </li>
        @endforeach
    </ul>
@endforeach
