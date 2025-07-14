<x-guest-layout title="{{ __('Meal Plan') }}">
    <h1 class="font-display text-center text-4xl mt-4 mb-8 font-semibold">
        {{ $event->title }} - {{ __('Meal Plan') }}
    </h1>
    <p class="text-center text-gray-700 mb-4">
        {{ $event->duration_string }}
    </p>

    @if($fullPlan ?? false)
        <div class="p-8">
            @include('partials.meal-plan', ['mealsByDate' => $mealsByDate, 'event' => $event])
        </div>
    @else
        @foreach($mealsByDate as $date => $meals)
            <section class="p-4 whitespace-nowrap text-gray-800 @if(!$loop->last) border-b border-gray-200 @endif">
                <div class="font-display font-bold text-lg pb-2 text-gray-600">
                    {{ \Illuminate\Support\Carbon::parse($date)->translatedFormat(__('l, j F Y')) }}
                </div>

                @foreach($meals as $meal)
                    <p>
                        <span class="font-bold">{{ $meal->title }}:</span><br/>
                        {{ $meal->recipes->map(fn($r) => $r->title)->join(', ') }}
                    </p>
                @endforeach
            </section>
        @endforeach
    @endif
</x-guest-layout>
