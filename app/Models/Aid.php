<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Aid extends Model
{
    //relaciones
    public function Beneficiary(): BelongsTo
    {
        return $this->belongsTo(Beneficiary::class);
    }
    public function Volunteer(): BelongsTo
    {
        return $this->belongsTo(Volunteer::class);
    }
    public function Collaborator(): BelongsTo
    {
        return $this->belongsTo(Collaborator::class);
    }
    public function giftCards(): HasMany
    {
        return $this->hasMany(GiftCard::class);
    }
}
