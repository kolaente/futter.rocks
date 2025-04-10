<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EventController extends Controller
{
    private function getMealsByDate(Event $event)
    {
        return $event->meals()
            ->orderBy('date')
            ->with('recipes')
            ->get()
            ->groupBy('date')
            ->map(fn($meals) => $meals->sortBy(function ($item) {
                $order = [
                    'Frühstück' => 1,
                    'Mittag' => 2,
                    'Abendessen' => 3
                ];

                return $order[$item->title] ?? 4;
            }));
    }

    public function generateMealPlan(Event $event)
    {
        Pdf::setOption([
            'dpi' => 300,
            'defaultFont' => 'sans-serif',
            'orientation' => 'portrait',
        ]);

        $pdf = Pdf::loadView('helper.meal-plan', [
            'event' => $event,
            'mealsByDate' => $this->getMealsByDate($event),
        ])->setPaper('a4', 'landscape');

        return $pdf->download($event->title . ' ' . __('Meal Plan') . '.pdf');
    }

    public function viewMealPlan(Event $event)
    {
        return view('helper.meal-plan', [
            'event' => $event,
            'mealsByDate' => $this->getMealsByDate($event),
        ]);
    }

    public function generateShoppingList(Event $event)
    {
        Pdf::setOption([
            'dpi' => 300,
            'defaultFont' => 'sans-serif',
            'orientation' => 'portrait',
        ]);

        $pdf = Pdf::loadView('helper.shopping-list', [
            'event' => $event,
            'list' => $event->getShoppingList(),
            'shoppingToursById' => $event->shoppingTours->keyBy('id'),
        ])->setPaper('a4', 'portrait');

        return $pdf->download($event->title . ' ' . __('Shopping List') . '.pdf');
    }

    public function viewShoppingList(Event $event)
    {
        return view('helper.shopping-list', [
            'event' => $event,
            'list' => $event->getShoppingList(),
            'shoppingToursById' => $event->shoppingTours->keyBy('id'),
        ]);
    }
}
