<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Scopes\CurrentTeam;
use Illuminate\Http\Request;

class SharedEventController extends Controller
{
    public function mealPlan(Request $request, string $shareId)
    {

        $event = Event::withoutGlobalScope(CurrentTeam::class)
            ->where('share_id', $shareId)
            ->firstOrFail();

        return view('pages.meal-plan', [
            'event' => $event,
            'mealsByDate' => $event->getMealsByDate(),
            'fullPlan' => $request->boolean('fullPlan'),
        ]);
    }

    public function shoppingList(string $shareId)
    {
        $event = Event::withoutGlobalScope(CurrentTeam::class)
            ->where('share_id', $shareId)
            ->firstOrFail();

        return view('pages.shopping-list', [
            'event' => $event,
        ]);
    }
}
