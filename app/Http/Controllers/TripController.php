<?php

namespace App\Http\Controllers;

use App\Enums\Currency;
use App\Events\TripInviteEvent;
use App\Events\TripUpdatedEvent;
use App\Http\Requests\TripRequest;
use App\Http\Requests\TripUpdateRequest;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class TripController extends Controller
{
    use AuthorizesRequests, ValidatesRequests;

    public function index(): Response
    {
        $user = Auth::user();

        $acceptedTrips = $user->trips()
            ->wherePivot('accepted', true)
            ->latest()
            ->get();

        $pendingInvites = $user->trips()
            ->wherePivot('accepted', false)
            ->latest()
            ->get();

        return Inertia::render('Trips/Sidebar', [
            'acceptedTrips' => $acceptedTrips,
            'pendingInvites' => $pendingInvites,
        ]);
    }

    public function show(Trip $trip): Response
    {
        $this->authorize('view', $trip);

        $trip->load(['creator', 'acceptedParticipants', 'pendingInvites']);

        return Inertia::render('Trips/Show', [
            'trip' => $trip,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Trips/Create', [
            'currencies' => Currency::values(),
        ]);
    }

    public function store(TripRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $trip = Trip::create([
            'creator_id' => auth()->id(),
            'title' => $validated['title'],
            'currency' => $validated['currency'],
        ]);

        if (!empty($validated['usernames'])) {
            $usersToInvite = User::whereIn('username', $validated['usernames'])->get();

            foreach ($usersToInvite as $user) {
                $trip->participants()->attach($user->id, ['accepted' => false]);

                $this->updateInviteCount($user);
            }
        }

        $trip->participants()->attach(auth()->id(), ['accepted' => true]);

        return redirect()->route('trips.show', $trip);
    }

    public function update(TripUpdateRequest $request, Trip $trip): RedirectResponse
    {
        $this->authorize('update', $trip);

        $validated = $request->validated();

        if (!empty($validated['usernames'])) {
            $usersToInvite = User::whereIn('username', $validated['usernames'])->get();

            foreach ($usersToInvite as $user) {
                if (!$trip->participants->contains($user->id)) {
                    $trip->participants()->attach($user->id, ['accepted' => false]);

                    $this->updateInviteCount($user);
                    $this->updateTrip($trip->id);
                }
            }
        }

        return redirect()->route('trips.show', $trip)->with('message', 'Trip updated.');
    }

    public function accept(Trip $trip): RedirectResponse
    {
        $user = Auth::user();

        $trip->participants()->updateExistingPivot($user->id, ['accepted' => true]);

        $this->updateInviteCount($user);
        $this->updateTrip($trip->id);

        return redirect()->route('trips.show', $trip);
    }

    public function decline(Trip $trip): RedirectResponse
    {
        $user = Auth::user();

        $trip->participants()->detach($user->id);

        $this->updateInviteCount($user);
        $this->updateTrip($trip->id);

        return redirect()->route('trips.invites')->with('message', 'Invite declined');
    }

    private function updateInviteCount($user): void
    {
        broadcast(new TripInviteEvent($user->id, $user->pendingTripInvites()->count()))
            ->toOthers();
    }

    private function updateTrip($tripId): void
    {
        broadcast(new TripUpdatedEvent($tripId))->toOthers();
    }
}
