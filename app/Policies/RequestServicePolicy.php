<?php

namespace App\Policies;

use App\Models\RequestService;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class RequestServicePolicy
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
        //
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\RequestService  $requestService
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, RequestService $requestService)
    {
        //
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(?User $user)
    {
    //    Log::info('RequestServicePolicy create');
        return true;
        //
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\RequestService  $requestService
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, RequestService $requestService)
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\RequestService  $requestService
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, RequestService $requestService)
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\RequestService  $requestService
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, RequestService $requestService)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\RequestService  $requestService
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, RequestService $requestService)
    {
        //
    }

    public function store(?User $user/*, RequestService $requestService*/)
    {
        return true;
    }
}
