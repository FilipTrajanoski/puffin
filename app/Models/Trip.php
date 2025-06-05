<?php

namespace App\Models;

use App\Enums\Currency;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Trip extends Model
{
    use HasFactory;

    protected $fillable = ['creator_id', 'title', 'currency'];

    protected $casts = [
        'currency' => Currency::class,
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function participants(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
                    ->withPivot('accepted')
                    ->withTimestamps() ;
    }

    public function pendingInvites(): BelongsToMany
    {
        return $this->participants()->wherePivot('accepted', false);
    }

    public function acceptedParticipants(): BelongsToMany
    {
        return $this->participants()->wherePivot('accepted', true);
    }
}
