<?php

namespace App\Policies;

use App\Models\Mode;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ModePolicy
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
        return $user->roles->first()->permissions->pluck('name')->contains('read-mode');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Mode  $mode
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Mode $mode)
    {
        return $user->roles->first()->permissions->pluck('name')->contains('read-mode');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return $user->roles->first()->permissions->pluck('name')->contains('create-mode');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Mode  $mode
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Mode $mode)
    {
        return $user->roles->first()->permissions->pluck('name')->contains('update-mode');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Mode  $mode
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Mode $mode)
    {
        return $user->roles->first()->permissions->pluck('name')->contains('delete-mode');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Mode  $mode
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, Mode $mode)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Mode  $mode
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, Mode $mode)
    {
        //
    }
}
