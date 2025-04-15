<?php

namespace App\Policies;

use App\Models\Request;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class RequestPolicy
{
    use HandlesAuthorization;

    public function update(User $user, Request $request)
    {
        return $user->id === $request->receiver_id;
    }

    public function view(User $user, Request $request)
    {
        return $user->id === $request->sender_id || $user->id === $request->receiver_id;
    }

    public function message(User $user, Request $request)
    {
        return ($user->id === $request->sender_id || $user->id === $request->receiver_id) && $request->status === 'accepted';
    }
}