<?php

use App\Livewire\Groups\CreateEdit;
use App\Models\ParticipantGroup;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Livewire\livewire;

it('can create a participant group', function () {
    $user = User::factory()->withCurrentTeam()->create();
    actingAs($user);

    livewire(CreateEdit::class)
        ->assertStatus(200)
        ->set('data', [
            'title' => 'Leaders',
            'food_factor' => 1.5,
        ])
        ->call('store')
        ->assertRedirect(route('participant-groups.list'));

    assertDatabaseHas('participant_groups', [
        'title' => 'Leaders',
        'food_factor' => 1.5,
        'team_id' => $user->current_team_id,
    ]);
});

it('can update an existing participant group', function () {
    $user = User::factory()->withCurrentTeam()->create();
    actingAs($user);

    $group = ParticipantGroup::factory()->create([
        'title' => 'Old Name',
        'food_factor' => 1,
        'team_id' => $user->current_team_id,
    ]);

    \Livewire\Livewire::withQueryParams(['group' => $group->id])
        ->test(CreateEdit::class)
        ->assertStatus(200)
        ->set('data', [
            'title' => 'New Name',
            'food_factor' => 2,
        ])
        ->call('store')
        ->assertRedirect(route('participant-groups.list'));

    assertDatabaseHas('participant_groups', [
        'id' => $group->id,
        'title' => 'New Name',
        'food_factor' => 2,
        'team_id' => $user->current_team_id,
    ]);
});
