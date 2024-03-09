<?php

namespace App\Policies;

use App\Models\PostSportHobby;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PostSportHobbyPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, PostSportHobby $postSportHobby): bool
    {
        //
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, PostSportHobby $postSportHobby): bool
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, PostSportHobby $postSportHobby): bool
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, PostSportHobby $postSportHobby): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, PostSportHobby $postSportHobby): bool
    {
        //
    }
}
