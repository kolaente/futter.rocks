<?php

use App\Models\Event;
use App\Models\Meal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

describe('Meal plan printing', function () {

    beforeEach(function () {
        $user = User::factory()->withCurrentTeam()->create();
        actingAs($user);

        $this->event = Event::factory()->create([
            'team_id' => $user->currentTeam->id,
            'created_by_id' => $user->id,
            'date_from' => '2026-08-10',
            'date_to' => '2026-08-15',
        ]);

        foreach ($this->event->date_from->toPeriod($this->event->date_to) as $day) {
            $meal = new Meal([
                'title' => 'Lunch',
                'date' => $day->format('Y-m-d'),
            ]);
            $meal->event_id = $this->event->id;
            $meal->save();
        }
    });

    it('starts every group of four days on a new page when printing', function () {
        $html = view('partials.meal-plan', [
            'mealsByDate' => $this->event->getMealsByDate(),
            'event' => $this->event,
        ])->render();

        // 6 days => 2 groups of 4 => 1 page break
        expect(substr_count($html, 'print:break-before-page'))->toBe(1);
    });

    it('hides the year in day headings when printing', function () {
        $html = view('partials.meal-plan', [
            'mealsByDate' => $this->event->getMealsByDate(),
            'event' => $this->event,
        ])->render();

        expect(substr_count($html, '<span class="print:hidden"> 2026</span>'))->toBe(6);
    });
});
