<?php

use App\Livewire\EventCreate;
use App\Livewire\EventList;
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
    Route::get('/events/create', EventCreate::class)->name('events.create');
});
