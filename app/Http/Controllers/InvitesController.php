<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class InvitesController extends Controller
{
    public function index(): Response
    {
        $user = Auth::user();

        $pendingInvites = $user->trips()
            ->wherePivot('accepted', false)
            ->with('creator')
            ->latest()
            ->get();

        return Inertia::render('Trips/Invites', [
            'pendingInvites' => $pendingInvites,
        ]);
    }
}
