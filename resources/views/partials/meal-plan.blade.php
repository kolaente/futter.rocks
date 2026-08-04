@php
    $fmt = new \App\Formatter();
    $in = 0;
    $editable ??= false;

    $days = $mealsByDate;
    if ($editable) {
        $days = collect();
        foreach ($event->date_from->toPeriod($event->date_to) as $day) {
            $days[$day->format('Y-m-d')] = $mealsByDate[$day->format('Y-m-d')] ?? collect();
        }
    }
@endphp

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-y-4">
    @foreach($days as $date => $meals)
        @php
            $in++;
            $responsiveClass = 'md:border-r-0';
            if ($in % 2 === 0) {
                $responsiveClass = 'border-r-1 lg:border-r-0';
            }
            if ($in % 4 === 0) {
                $responsiveClass = 'lg:border-r-1';
            }

            if($in === count($days)) {
                $responsiveClass = 'border-r-1';
            }
        @endphp
        <div class="bg-white border {{ $responsiveClass }} overflow-hidden h-full">
            <div class="bg-gray-50 px-4 pt-3 pb-1 border-b">
                <h3 class="font-semibold font-display text-lg text-gray-800">
                    {{ \Illuminate\Support\Carbon::parse($date)->translatedFormat(__('l, j F Y')) }}
                </h3>
            </div>
            <div
                class="p-4 h-full"
                @if($editable)
                    x-sort="$wire.reorderMeal($item, $position, '{{ $date }}')"
                    x-sort:group="meals"
                    x-sort:config="{ ghostClass: 'opacity-50' }"
                @endif
            >
                @if($editable && $meals->isEmpty())
                    <p x-sort:ignore class="rounded-lg border-2 border-dashed border-gray-300 p-4 text-center text-sm text-gray-400">
                        {{ __('No meals') }}
                    </p>
                @endif
                @foreach($meals as $meal)
                    <div
                        class="mb-4 last:mb-0 @if($editable) rounded-lg p-2 -mx-2 hover:bg-gray-50 @endif"
                        @if($editable)
                            x-sort:item="{{ $meal->id }}"
                            wire:key="meal-{{ $meal->id }}"
                        @endif
                    >
                        <div class="@if($editable) flex items-start gap-2 @endif">
                            @if($editable)
                                <button type="button" x-sort:handle class="mt-1 cursor-grab touch-none text-gray-400 hover:text-gray-600" title="{{ __('Drag to reorder') }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-4">
                                        <circle cx="7" cy="5" r="1.5"/>
                                        <circle cx="13" cy="5" r="1.5"/>
                                        <circle cx="7" cy="10" r="1.5"/>
                                        <circle cx="13" cy="10" r="1.5"/>
                                        <circle cx="7" cy="15" r="1.5"/>
                                        <circle cx="13" cy="15" r="1.5"/>
                                    </svg>
                                </button>
                            @endif
                            <div class="@if($editable) flex-1 min-w-0 @endif">
                                <h4 class="font-semibold text-gray-800 mb-2">
                                    {{ $meal->title }}
                                    @if($meal->multiplier !== null && $meal->multiplier != 1)
                                        <span class="text-gray-500 text-sm font-normal">×{{ $fmt->format($meal->multiplier) }}</span>
                                    @endif
                                </h4>
                                @foreach($meal->recipes as $recipe)
                                    <div class="ml-2 mb-3 last:mb-0">
                                        <em class="text-gray-700 font-medium">{{ $recipe->title }}:</em>
                                        <ul class="mt-1 ml-6 list-disc marker:text-gray-300 space-y-1">
                                            @foreach($recipe->getCalculatedIngredientsForEvent($event, $meal) as $item)
                                                <li>
                                                    {{ $fmt->format($item['quantity']) }} {{ $item['unit']->getShortLabel() }} {{ $item['ingredient']->title }}
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endforeach
                            </div>
                            @if($editable)
                                <div class="flex flex-col">
                                    <button
                                        type="button"
                                        wire:click="moveMeal({{ $meal->id }}, -1)"
                                        @disabled($loop->first)
                                        class="text-gray-400 hover:text-gray-600 disabled:opacity-25 disabled:hover:text-gray-400"
                                        title="{{ __('Move up') }}"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5">
                                            <path fill-rule="evenodd" d="M14.77 12.79a.75.75 0 0 1-1.06-.02L10 8.832 6.29 12.77a.75.75 0 1 1-1.08-1.04l4.25-4.5a.75.75 0 0 1 1.08 0l4.25 4.5a.75.75 0 0 1-.02 1.06Z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                    <button
                                        type="button"
                                        wire:click="moveMeal({{ $meal->id }}, 1)"
                                        @disabled($loop->last)
                                        class="text-gray-400 hover:text-gray-600 disabled:opacity-25 disabled:hover:text-gray-400"
                                        title="{{ __('Move down') }}"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5">
                                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.168l3.71-3.938a.75.75 0 1 1 1.08 1.04l-4.25 4.5a.75.75 0 0 1-1.08 0l-4.25-4.5a.75.75 0 0 1 .02-1.06Z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endforeach
</div>
