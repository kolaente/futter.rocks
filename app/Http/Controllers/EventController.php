<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EventController extends Controller
{
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
