@php
    $i = 1;
    $paginated = $mealsByDate->forPage($i, 4);
    $fmt = new NumberFormatter('de_DE', NumberFormatter::DECIMAL);
@endphp

@while($paginated->count() > 0)
    @php
        $paginated = $mealsByDate->forPage($i, 4);
        $i++;
    @endphp
    <table class="border table-fixed w-full">
        <thead>
        <tr>
            @foreach($paginated as $date => $meal)
                <th class="p-2 pb-0 font-semibold font-display text-lg border">
                    {{ $date }}
                </th>
            @endforeach
        </tr>
        </thead>
        <tr>
            @foreach($paginated as $meals)
                <td class="p-2 border align-top">
                    @foreach($meals as $meal)
                        <span class="pb-1 block font-semibold">
                            {{ $meal->title }}:
                        </span>
                        @foreach($meal->recipes as $recipe)
                            <em>{{ $recipe->title }}:</em>
                            <ul class="mb-3 pl-6 list-disc marker:text-gray-300">
                                @foreach($recipe->getCalculatedIngredientsForEvent($event) as $item)
                                    <li>{{ $fmt->format($item['quantity']) }} {{ $item['unit']->getShortLabel() }} {{ $item['ingredient']->title }}</li>
                                @endforeach
                            </ul>
                        @endforeach
                    @endforeach
                </td>
            @endforeach
        </tr>
    </table>
@endwhile
