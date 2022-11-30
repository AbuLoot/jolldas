<?php

namespace App\Policies;

use App\Models\Language;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class LanguagePolicy
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
        return $user->roles->first()->permissions->pluck('name')->contains('read-language');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Language  $language
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Language $language)
    {
        return $user->roles->first()->permissions->pluck('name')->contains('read-language');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return $user->roles->first()->permissions->pluck('name')->contains('create-language');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Language  $language
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Language $language)
    {
        return $user->roles->first()->permissions->pluck('name')->contains('update-language');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Language  $language
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Language $language)
    {
        return $user->roles->first()->permissions->pluck('name')->contains('delete-language');
    }
}
