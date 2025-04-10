<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class CurrentTeam implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        $builder->where('team_id', Auth::user()->currentTeam->id);
    }
}
