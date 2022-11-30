<?php

namespace App\Policies;

use App\Models\App;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AppPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        return $user->roles->first()->permissions->pluck('name')->contains('read-app');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\App  $app
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, App $app)
    {
        return $user->roles->first()->permissions->pluck('name')->contains('read-app');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return $user->roles->first()->permissions->pluck('name')->contains('create-app');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\App  $app
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, App $app)
    {
        return $user->roles->first()->permissions->pluck('name')->contains('update-app');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\App  $app
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, App $app)
    {
        return $user->roles->first()->permissions->pluck('name')->contains('delete-app');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\App  $app
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, App $app)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\App  $app
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, App $app)
    {
        //
    }
}
