<?php

use App\Http\Controllers\SharedEventController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::view('/dashboard', 'dashboard')->name('dashboard');
    Route::get('/events', \App\Livewire\Events\All::class)->name('events.list');
    Route::get('/events/create', \App\Livewire\Events\CreateEdit::class)->name('events.create');
    Route::get('/events/{event}/edit', \App\Livewire\Events\CreateEdit::class)->name('events.edit');
    Route::get('/events/{event}', \App\Livewire\Events\View::class)->name('events.view');
    Route::get('/events/{event}/meal-plan', \App\Livewire\Events\MealPlan::class)->name('events.meal-plan');
    Route::get('/events/{event}/shopping-list', \App\Livewire\Events\ShoppingList::class)->name('events.shopping-list');

    Route::get('/recipes', \App\Livewire\Recipes\All::class)->name('recipes.list');
    Route::get('/recipes/create', \App\Livewire\Recipes\CreateEdit::class)->name('recipes.create');
    Route::get('/recipes/{recipe}', \App\Livewire\Recipes\View::class)->name('recipes.view');
    Route::get('/recipes/{recipe}/edit', \App\Livewire\Recipes\CreateEdit::class)->name('recipes.edit');

    Route::get('/participant-groups', \App\Livewire\Groups\All::class)->name('participant-groups.list');
    Route::get('/participant-groups/create', \App\Livewire\Groups\CreateEdit::class)->name('participant-groups.create');
    Route::get('/participant-groups/{group}/edit', \App\Livewire\Groups\CreateEdit::class)->name('participant-groups.edit');
});

Route::get('/shared/events/{event:share_id}/meal-plan', [SharedEventController::class, 'mealPlan'])->name('shared.event.meal-plan');
