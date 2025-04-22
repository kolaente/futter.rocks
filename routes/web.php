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
    Route::get('/dashboard', \App\Http\Controllers\DashboardController::class)->name('dashboard');

    Route::prefix('events')->name('events.')->group(function () {
        Route::get('/', \App\Livewire\Events\All::class)->name('list');
        Route::get('/create', \App\Livewire\Events\CreateEdit::class)->name('create');

        Route::middleware('can:view,event')->group(function () {
            Route::get('/{event}', \App\Livewire\Events\View::class)->name('view');
            Route::get('/{event}/meal-plan', \App\Livewire\Events\MealPlan::class)->name('meal-plan');
            Route::get('/{event}/shopping-list', \App\Livewire\Events\ShoppingList::class)->name('shopping-list');
        });

        Route::get('/{event}/edit', \App\Livewire\Events\CreateEdit::class)
            ->middleware('can:update,event')
            ->name('edit');
    });

    Route::prefix('recipes')->name('recipes.')->group(function () {
        Route::get('/', \App\Livewire\Recipes\All::class)->name('list');
        Route::get('/create', \App\Livewire\Recipes\CreateEdit::class)->name('create');

        Route::get('/{recipe}', \App\Livewire\Recipes\View::class)
            ->middleware('can:view,recipe')
            ->name('view');

        Route::get('/{recipe}/edit', \App\Livewire\Recipes\CreateEdit::class)
            ->middleware('can:update,recipe')
            ->name('edit');
    });

    Route::prefix('participant-groups')->name('participant-groups.')->group(function () {
        Route::get('/', \App\Livewire\Groups\All::class)->name('list');
        Route::get('/create', \App\Livewire\Groups\CreateEdit::class)->name('create');

        Route::get('/{group}/edit', \App\Livewire\Groups\CreateEdit::class)
            ->middleware('can:update,group')
            ->name('edit');
    });
});

Route::get('/shared/events/{event:share_id}/meal-plan', [SharedEventController::class, 'mealPlan'])->name('shared.event.meal-plan');
Route::get('/privacy-policy', fn () => view('components.markdown-content', ['file' => 'policy.md', 'title' => __('Privacy Policy')]))
    ->name('policy.show');
Route::get('/imprint', fn () => view('components.markdown-content', ['file' => 'imprint.md', 'title' => __('Imprint')]))
    ->name('imprint');
