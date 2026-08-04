<?php

use App\Livewire\Events\MealPlan;
use App\Models\Event;
use App\Models\Meal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

uses(RefreshDatabase::class);

function createReorderEvent(User $user): Event
{
    return Event::factory()->create([
        'team_id' => $user->currentTeam->id,
        'created_by_id' => $user->id,
        'date_from' => '2026-08-10',
        'date_to' => '2026-08-12',
    ]);
}

function createMealAt(Event $event, string $title, string $date, ?int $position = null): Meal
{
    $meal = new Meal([
        'title' => $title,
        'date' => $date,
    ]);
    $meal->event_id = $event->id;
    if ($position !== null) {
        $meal->position = $position;
    }
    $meal->save();

    return $meal;
}

describe('Meal reorder', function () {

    it('orders meals by position in getMealsByDate', function () {
        $user = User::factory()->withCurrentTeam()->create();
        actingAs($user);

        $event = createReorderEvent($user);
        createMealAt($event, 'Zebra', '2026-08-10', 1);
        createMealAt($event, 'Abendessen', '2026-08-10', 2);
        createMealAt($event, 'Frühstück', '2026-08-10', 3);

        $mealsByDate = $event->getMealsByDate();

        expect($mealsByDate->get('2026-08-10')->pluck('title')->values()->all())
            ->toBe(['Zebra', 'Abendessen', 'Frühstück']);
    });

    it('reorders meals within a day and renumbers positions', function () {
        $user = User::factory()->withCurrentTeam()->create();
        actingAs($user);

        $event = createReorderEvent($user);
        $breakfast = createMealAt($event, 'Frühstück', '2026-08-10');
        $lunch = createMealAt($event, 'Mittagessen', '2026-08-10');
        $dinner = createMealAt($event, 'Abendessen', '2026-08-10');

        livewire(MealPlan::class, ['event' => $event])
            ->call('reorderMeal', $dinner->id, 0, '2026-08-10');

        expect($dinner->refresh()->position)->toBe(1)
            ->and($breakfast->refresh()->position)->toBe(2)
            ->and($lunch->refresh()->position)->toBe(3);
    });

    it('moves a meal to another day and renumbers both days', function () {
        $user = User::factory()->withCurrentTeam()->create();
        actingAs($user);

        $event = createReorderEvent($user);
        $breakfast = createMealAt($event, 'Frühstück', '2026-08-10');
        $lunch = createMealAt($event, 'Mittagessen', '2026-08-10');
        $otherBreakfast = createMealAt($event, 'Frühstück', '2026-08-11');

        livewire(MealPlan::class, ['event' => $event])
            ->call('reorderMeal', $breakfast->id, 1, '2026-08-11');

        expect($breakfast->refresh()->date->format('Y-m-d'))->toBe('2026-08-11')
            ->and($otherBreakfast->refresh()->position)->toBe(1)
            ->and($breakfast->position)->toBe(2)
            ->and($lunch->refresh()->position)->toBe(1);
    });

    it('rejects reordering meals of another event', function () {
        $user = User::factory()->withCurrentTeam()->create();
        actingAs($user);

        $event = createReorderEvent($user);
        $otherEvent = createReorderEvent($user);
        $foreignMeal = createMealAt($otherEvent, 'Frühstück', '2026-08-10');

        livewire(MealPlan::class, ['event' => $event])
            ->call('reorderMeal', $foreignMeal->id, 0, '2026-08-10');
    })->throws(Illuminate\Database\Eloquent\ModelNotFoundException::class);

    it('rejects dates outside the event range', function () {
        $user = User::factory()->withCurrentTeam()->create();
        actingAs($user);

        $event = createReorderEvent($user);
        $meal = createMealAt($event, 'Frühstück', '2026-08-10');

        livewire(MealPlan::class, ['event' => $event])
            ->call('reorderMeal', $meal->id, 0, '2026-08-20')
            ->assertStatus(422);

        expect($meal->refresh()->date->format('Y-m-d'))->toBe('2026-08-10');
    });

    it('swaps positions with the neighbor via moveMeal', function () {
        $user = User::factory()->withCurrentTeam()->create();
        actingAs($user);

        $event = createReorderEvent($user);
        $breakfast = createMealAt($event, 'Frühstück', '2026-08-10');
        $lunch = createMealAt($event, 'Mittagessen', '2026-08-10');
        $dinner = createMealAt($event, 'Abendessen', '2026-08-10');

        livewire(MealPlan::class, ['event' => $event])
            ->call('moveMeal', $lunch->id, 1);

        expect($lunch->refresh()->position)->toBe(3)
            ->and($dinner->refresh()->position)->toBe(2)
            ->and($breakfast->refresh()->position)->toBe(1);

        livewire(MealPlan::class, ['event' => $event])
            ->call('moveMeal', $lunch->id, -1);

        expect($lunch->refresh()->position)->toBe(2)
            ->and($dinner->refresh()->position)->toBe(3);
    });

    it('does nothing when moving past the edge of a day', function () {
        $user = User::factory()->withCurrentTeam()->create();
        actingAs($user);

        $event = createReorderEvent($user);
        $breakfast = createMealAt($event, 'Frühstück', '2026-08-10');
        $lunch = createMealAt($event, 'Mittagessen', '2026-08-10');

        livewire(MealPlan::class, ['event' => $event])
            ->call('moveMeal', $breakfast->id, -1);

        expect($breakfast->refresh()->position)->toBe(1)
            ->and($lunch->refresh()->position)->toBe(2);
    });

    it('appends new meals to the end of their day', function () {
        $user = User::factory()->withCurrentTeam()->create();
        actingAs($user);

        $event = createReorderEvent($user);
        createMealAt($event, 'Frühstück', '2026-08-10');
        createMealAt($event, 'Mittagessen', '2026-08-10');
        $otherDay = createMealAt($event, 'Frühstück', '2026-08-11');

        expect(Meal::query()->whereDate('date', '2026-08-10')->orderBy('position')->pluck('position', 'title')->all())
            ->toBe(['Frühstück' => 1, 'Mittagessen' => 2])
            ->and($otherDay->position)->toBe(1);
    });

    it('appends a meal to the end of the target day when its date is edited', function () {
        $user = User::factory()->withCurrentTeam()->create();
        actingAs($user);

        $event = createReorderEvent($user);
        createMealAt($event, 'Frühstück', '2026-08-10');
        $moved = createMealAt($event, 'Mittagessen', '2026-08-10');
        createMealAt($event, 'Frühstück', '2026-08-11');

        $moved->update(['date' => '2026-08-11']);

        expect($moved->refresh()->position)->toBe(2);
    });

    it('backfills positions from the old name-based order', function () {
        $user = User::factory()->withCurrentTeam()->create();
        actingAs($user);

        $event = createReorderEvent($user);

        Schema::table('meals', function ($table) {
            $table->dropColumn('position');
        });

        DB::table('meals')->insert([
            ['event_id' => $event->id, 'title' => 'Abendessen', 'date' => '2026-08-10', 'created_at' => now(), 'updated_at' => now()],
            ['event_id' => $event->id, 'title' => 'Frühstück', 'date' => '2026-08-10', 'created_at' => now(), 'updated_at' => now()],
            ['event_id' => $event->id, 'title' => 'Mittagessen', 'date' => '2026-08-10', 'created_at' => now(), 'updated_at' => now()],
            ['event_id' => $event->id, 'title' => 'Frühstück', 'date' => '2026-08-11', 'created_at' => now(), 'updated_at' => now()],
            ['event_id' => $event->id, 'title' => 'Snack', 'date' => '2026-08-11', 'created_at' => now(), 'updated_at' => now()],
        ]);

        $migration = require database_path('migrations/2026_08_04_203100_add_position_to_meals_table.php');
        $migration->up();

        $day1 = Meal::query()->whereDate('date', '2026-08-10')->orderBy('position')->get();
        expect($day1->pluck('title')->all())->toBe(['Frühstück', 'Mittagessen', 'Abendessen'])
            ->and($day1->pluck('position')->all())->toBe([1, 2, 3]);

        $day2 = Meal::query()->whereDate('date', '2026-08-11')->orderBy('position')->get();
        expect($day2->pluck('title')->all())->toBe(['Frühstück', 'Snack'])
            ->and($day2->pluck('position')->all())->toBe([1, 2]);
    });
});
