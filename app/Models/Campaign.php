<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Campaign extends Model
{
    protected $fillable = [
        'title',
        'description',
        'target_amount',
        'status',
    ];

    protected $casts = [
        'target_amount' => 'decimal:2',
    ];

    public function donations(): HasMany
    {
        return $this->hasMany(Donation::class);
    }

    public function total(): HasOne
    {
        return $this->hasOne(DonationTotal::class, 'campaign_id');
    }
}
