<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Recipe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $currentTeam = Auth::user()->currentTeam;

        $todaysMeals = collect();
        $participantCount = 0;
        $recentRecipes = collect();

        // Fetch the latest event based on start date for the current team
        $currentEvent = Event::where('team_id', $currentTeam->id)
            ->orderBy('date_from', 'desc')
            ->with('participantGroups', 'meals')
            ->first();

        if ($currentEvent) {
            $participantCount = $currentEvent->participantGroups->sum('pivot.quantity');
            $recipeCount = Recipe::whereIn('id', $currentEvent
                ->meals
                ->pluck('recipes')
                ->flatten()
                ->pluck('id'))
                ->count();
        }

        $recentRecipes = Recipe::where('team_id', $currentTeam->id)
            ->orderBy('updated_at', 'desc')
            ->limit(5)
            ->get();

        return view('dashboard', [
            'currentEvent' => $currentEvent,
            'participantCount' => $participantCount,
            'recentRecipes' => $recentRecipes,
            'recipeCount' => $recipeCount,
        ]);
    }
}
