<?php

use App\Models\Trip;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('trip-invites.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});

Broadcast::channel('trip-updates.{tripId}', function ($user, $tripId) {
    return Trip::find($tripId)->participants->contains($user->id);
});
