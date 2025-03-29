<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\Request;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('chat.{requestId}', function ($user, $requestId) {
    $request = Request::find($requestId);

    if (!$request) {
        return false;
    }

    return $user->id === $request->sender_id || $user->id === $request->receiver_id;
});
