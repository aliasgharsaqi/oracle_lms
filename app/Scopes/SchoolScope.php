<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class SchoolScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
        // IMPORTANT: Check if the application is running in the console
        if (app()->runningInConsole()) {
            // If it is, do not apply the scope and exit the method.
            return;
        }

        // The original logic is now safe to run for web requests.
        // It's also slightly safer to get the user into a variable first.
        $user = Auth::user();

        if ($user && $user->school_id && !$user->hasRole('Super Admin')) {
            $builder->where($model->getTable() . '.school_id', $user->school_id);
        }
    }
}
