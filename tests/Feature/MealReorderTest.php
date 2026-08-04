<?php

use App\Models\Event;
use App\Models\Meal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

use function Pest\Laravel\actingAs;

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
