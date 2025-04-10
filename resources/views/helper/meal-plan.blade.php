<style>
    * {
        font-family: sans-serif;
        line-height: 1.25;
    }

    .header * {
        text-align: center;
    }

    table,
    thead,
    tbody,
    tfoot,
    tr,
    th,
    td {
        width: auto;
        height: auto;
        margin: 0;
        padding: 0;
        border-collapse: inherit;
        border-spacing: 0;
        text-align: left;
        -webkit-border-horizontal-spacing: 0;
        -webkit-border-vertical-spacing: 0;
        font-size: .75rem;
    }

    table {
        border: 1px solid #ccc;
        border-left: none;
        page-break-before: always;
    }

    th, td {
        padding: .25rem .5rem;
        border-left: 1px solid #ccc;
        vertical-align: top;
    }

    thead th {
        border-bottom: 1px solid #ccc;
    }

    p {
        margin: .5rem 0 .5rem;
    }

    .meal-title {
        padding-bottom: .5rem;
        display: block;
        font-weight: bold;
    }

    .meal-ingredients {
        margin-top: 0;
        margin-bottom: .25rem;
        padding-left: 1.5rem;
    }
</style>

@php
    $fmt = new NumberFormatter('de_DE', NumberFormatter::DECIMAL);
@endphp

<div>
    <div class="header">
        @include('partials.event-pdf-header', ['event' => $event, 'subtitle' => __('Meal Plan')])
    </div>

    @php
        $i = 1;
        $paginated = $mealsByDate->forPage($i, 4);
    @endphp

    @while($paginated->count() > 0)
        @php
            $paginated = $mealsByDate->forPage($i, 4);
            $i++;
        @endphp
        <table>
            <thead>
            <tr>
                @foreach($paginated as $date => $meal)
                    <th>
                        {{ $date }}
                    </th>
                @endforeach
            </tr>
            </thead>
            <tr>
                @foreach($paginated as $meals)
                    <td>
                        @foreach($meals as $meal)
                            <span class="meal-title">{{ $meal->title }}:</span>
                            @foreach($meal->recipes as $recipe)
                                <em>{{ $recipe->title }}:</em>
                                <ul class="meal-ingredients">
                                    @foreach($recipe->getCalculatedIngredientsForEvent($event) as $item)
                                        <li>{{ $fmt->format($item['quantity']) }} {{ $item['ingredient']->unit->getShortLabel() }} {{ $item['ingredient']->title }}</li>
                                    @endforeach
                                </ul>
                            @endforeach
                        @endforeach
                    </td>
                @endforeach
            </tr>
        </table>
    @endwhile
</div>
