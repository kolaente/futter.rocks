@php
    $fmt = new \App\Formatter();
    $in = 0;
@endphp

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-y-4">
    @foreach($mealsByDate as $date => $meals)
        @php
            $in++;
            $responsiveClass = 'md:border-r-0';
            if ($in % 2 === 0) {
                $responsiveClass = 'border-r-1 lg:border-r-0';
            }
            if ($in % 4 === 0) {
                $responsiveClass = 'lg:border-r-1';
            }

            if($in === count($mealsByDate)) {
                $responsiveClass = 'border-r-1';
            }
        @endphp
        <div class="bg-white border {{ $responsiveClass }} overflow-hidden h-full">
            <div class="bg-gray-50 px-4 pt-3 pb-1 border-b">
                <h3 class="font-semibold font-display text-lg text-gray-800">
                    {{ \Illuminate\Support\Carbon::parse($date)->translatedFormat(__('l, j F Y')) }}
                </h3>
            </div>
            <div class="p-4">
                @foreach($meals as $meal)
                    <div class="mb-4 last:mb-0">
                        <h4 class="font-semibold text-gray-800 mb-2">
                            {{ $meal->title }}
                        </h4>
                        @foreach($meal->recipes as $recipe)
                            <div class="ml-2 mb-3 last:mb-0">
                                <em class="text-gray-700 font-medium">{{ $recipe->title }}:</em>
                                <ul class="mt-1 ml-6 list-disc marker:text-gray-300 space-y-1">
                                    @foreach($recipe->getCalculatedIngredientsForEvent($event) as $item)
                                        <li>
                                            {{ $fmt->format($item['quantity']) }} {{ $item['unit']->getShortLabel() }} {{ $item['ingredient']->title }}
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>
        </div>
    @endforeach
</div>
