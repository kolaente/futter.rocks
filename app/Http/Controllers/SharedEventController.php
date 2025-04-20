<?php

namespace App\Http\Controllers;

use App\Models\Event;

class SharedEventController extends Controller
{
    public function mealPlan(Event $event)
    {
        return view('pages.meal-plan', [
            'event' => $event,
            'mealsByDate' => $event->getMealsByDate(),
        ]);
    }
}
