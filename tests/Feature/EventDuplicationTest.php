<?php

namespace Tests\Feature;

use App\Livewire\Events\View as EventView;
use App\Models\Event;
use App\Models\Meal;
use App\Models\ParticipantGroup;
use App\Models\Recipe;
use App\Models\ShoppingTour;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class EventDuplicationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_duplicate_event(): void
    {
        $event = Event::factory()->create();

        Livewire::test(EventView::class, ['event' => $event])
            ->call('duplicateEvent')
            ->assertForbidden(); // Or assertRedirect(route('login')) depending on middleware
    }

    public function test_authorized_user_can_duplicate_event(): void
    {
        $user = User::factory()->create();
        $team = Team::factory()->create(['user_id' => $user->id, 'personal_team' => true]);
        $user->current_team_id = $team->id;
        $user->save();

        $originalEvent = Event::factory()->recycle($team)->create(['created_by_id' => $user->id]);

        // Create related data
        $recipe = Recipe::factory()->recycle($team)->create();
        $meal = Meal::create([
            'event_id' => $originalEvent->id,
            'name' => 'Test Lunch',
            'date' => $originalEvent->date_from->addDays(1),
        ]);
        $meal->recipes()->attach($recipe->id);

        $participantGroup = ParticipantGroup::factory()->recycle($team)->create();
        $originalEvent->participantGroups()->attach($participantGroup->id, ['quantity' => 5]);

        $shoppingTour = ShoppingTour::create([
            'event_id' => $originalEvent->id,
            'date' => $originalEvent->date_from->subDay(),
        ]);

        $this->actingAs($user);

        $response = Livewire::test(EventView::class, ['event' => $originalEvent])
            ->call('duplicateEvent');

        // Assert new event creation and basic attributes
        $this->assertDatabaseCount('events', 2);
        $newEvent = Event::where('title', __('Copy of').' '.$originalEvent->title)->first();

        $this->assertNotNull($newEvent);
        $this->assertNotEquals($originalEvent->id, $newEvent->id);
        $this->assertEquals(__('Copy of').' '.$originalEvent->title, $newEvent->title);
        $this->assertEquals($originalEvent->description, $newEvent->description);
        $this->assertEquals($originalEvent->date_from, $newEvent->date_from);
        $this->assertEquals($originalEvent->date_to, $newEvent->date_to);
        $this->assertEquals($originalEvent->team_id, $newEvent->team_id);
        $this->assertEquals($originalEvent->created_by_id, $newEvent->created_by_id); // As per current implementation
        $this->assertNotNull($newEvent->share_id);
        $this->assertNotEquals($originalEvent->share_id, $newEvent->share_id);

        // Assert relations count
        $this->assertCount(1, $newEvent->meals);
        $this->assertCount(1, $newEvent->participantGroups);
        $this->assertCount(1, $newEvent->shoppingTours);

        // Assert meal details
        $newMeal = $newEvent->meals->first();
        $this->assertEquals($meal->name, $newMeal->name);
        $this->assertEquals($meal->date->toDateString(), $newMeal->date->toDateString()); // Compare date strings
        $this->assertCount(1, $newMeal->recipes);
        $this->assertEquals($recipe->id, $newMeal->recipes->first()->id);

        // Assert participant group details
        $newParticipantGroup = $newEvent->participantGroups->first();
        $this->assertEquals($participantGroup->id, $newParticipantGroup->id);
        $this->assertEquals(5, $newParticipantGroup->pivot->quantity);

        // Assert shopping tour details
        $newShoppingTour = $newEvent->shoppingTours->first();
        $this->assertEquals($shoppingTour->date->toDateString(), $newShoppingTour->date->toDateString()); // Compare date strings

        // Assert redirection
        $response->assertRedirect(route('events.view', ['event' => $newEvent]));

        // Optionally, assert notification - this can be tricky.
        // For now, successful redirect and data consistency is a strong indicator.
        // Notification::assertSent( ... );
    }

    public function test_authorized_user_can_duplicate_event_with_no_relations(): void
    {
        $user = User::factory()->create();
        $team = Team::factory()->create(['user_id' => $user->id, 'personal_team' => true]);
        $user->current_team_id = $team->id;
        $user->save();

        $originalEvent = Event::factory()->recycle($team)->create(['created_by_id' => $user->id]);

        $this->actingAs($user);

        $response = Livewire::test(EventView::class, ['event' => $originalEvent])
            ->call('duplicateEvent');

        $this->assertDatabaseCount('events', 2);
        $newEvent = Event::where('title', __('Copy of').' '.$originalEvent->title)->first();

        $this->assertNotNull($newEvent);
        $this->assertNotEquals($originalEvent->id, $newEvent->id);
        $this->assertEquals(__('Copy of').' '.$originalEvent->title, $newEvent->title);
        $this->assertEquals($originalEvent->description, $newEvent->description);
        $this->assertEquals($originalEvent->date_from, $newEvent->date_from);
        $this->assertEquals($originalEvent->date_to, $newEvent->date_to);
        $this->assertEquals($originalEvent->team_id, $newEvent->team_id);
        $this->assertEquals($originalEvent->created_by_id, $newEvent->created_by_id);

        $this->assertCount(0, $newEvent->meals);
        $this->assertCount(0, $newEvent->participantGroups);
        $this->assertCount(0, $newEvent->shoppingTours);

        $response->assertRedirect(route('events.view', ['event' => $newEvent]));
    }
}
