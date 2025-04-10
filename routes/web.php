<?php

use App\Livewire\Events\EventList;
use App\Livewire\Events\View;
use App\Livewire\Events\Create;
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
    Route::get('/events', EventList::class)->name('events.list');
    Route::get('/events/create', Create::class)->name('events.create');
    Route::get('/events/{event}', View::class)->name('events.view');
});
