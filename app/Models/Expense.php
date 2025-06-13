<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'trip_id', 'paid_by', 'description', 'amount', 'date', 'split_method'
    ];

    public function trip(): BelongsTo
    {
        return $this->belongsTo(Trip::class);
    }

    public function payer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'paid_by');
    }

    public function participants()
    {
        return $this->belongsToMany(User::class)
            ->withPivot('share', 'paid_share', 'owed_share')
            ->withTimestamps();
    }

    public function getSettlementAttribute(): array
    {
        $settlement = [];

        foreach ($this->participants as $participant) {
            if ($participant->id !== $this->paid_by) {
                $settlement[] = [
                    'from' => $participant->id,
                    'to' => $this->paid_by,
                    'amount' => $participant->pivot->owed_share
                ];
            }
        }

        return $settlement;
    }
}
