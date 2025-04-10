<style>
    * {
        font-family: sans-serif;
        line-height: 1.5;
        text-align: center;
    }

    p {
        margin: .5rem 0 .5rem;
    }

    .shopping-tour-list {
        text-align: left;
    }

    ul, .shopping-tour-list {
        padding-left: 2rem;
    }

    ul li {
        text-align: left;
        font-size: .75rem;
        list-style-type: none;
    }

    .check {
        display: inline-block;
        border: 1px solid #000;
        width: .75em;
        height: .75em;
        vertical-align: baseline;
        margin-bottom: -1px;
    }
</style>

@php
    $fmt = new NumberFormatter('de_DE', NumberFormatter::DECIMAL);
@endphp

<div>
    @include('partials.event-pdf-header', ['event' => $event, 'subtitle' => __('Shopping List')])

    @foreach($list as $shoppingTourId => $tourList)

        @if(count($list) > 1)
            <p class="shopping-tour-list">
                @if($shoppingTourId === 0)
                    Vor der Veranstaltung:
                @else
                    Am {{ $shoppingToursById->get($shoppingTourId)->date }}:
                @endif
            </p>
        @endif

        <ul>
            @foreach($tourList as $item)
                <li>
                    <span class="check"></span>
                    {{ $fmt->format($item['quantity']) }} {{ $item['ingredient']->unit->getShortLabel() }} {{ $item['ingredient']->title }}
                </li>
            @endforeach
        </ul>
    @endforeach
</div>
