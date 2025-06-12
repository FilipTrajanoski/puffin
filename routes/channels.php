<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('trip-invites.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});
