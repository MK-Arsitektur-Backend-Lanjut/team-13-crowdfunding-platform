<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Campaign extends Model
{
    protected $fillable = [
        'title',
        'description',
        'target_amount',
        'status',
    ];

    protected $casts = [
        'target_amount'   => 'decimal:2',
        'total_donations' => 'decimal:2',
    ];

    public function donations(): HasMany
    {
        return $this->hasMany(Donation::class);
    }

    /**
     * Eager-load total_donations dari tabel donation_totals (denormalized).
     * 
     */
    public function scopeWithTotalDonations(Builder $query): Builder
    {
        return $query->addSelect([
            'total_donations' => DB::table('donation_totals')
                ->selectRaw('COALESCE(total_amount, 0)')
                ->whereColumn('campaign_id', 'campaigns.id'),
        ]);
    }
}
