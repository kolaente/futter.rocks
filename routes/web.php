<?php

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
    Route::get('/events/create', \App\Livewire\Events\Create::class)->name('events.create');
    Route::get('/events/{event}', \App\Livewire\Events\View::class)->name('events.view');

    Route::get('/recipes', \App\Livewire\Recipes\All::class)->name('recipes.list');
    Route::get('/recipes/create', \App\Livewire\Recipes\CreateEdit::class)->name('recipes.create');
    Route::get('/recipes/{recipe}', \App\Livewire\Recipes\View::class)->name('recipes.view');
    Route::get('/recipes/{recipe}/edit', \App\Livewire\Recipes\CreateEdit::class)->name('recipes.edit');
});
