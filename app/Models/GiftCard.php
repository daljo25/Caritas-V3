<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class GiftCard extends Model
{
    use HasFactory;

    protected $fillable = [
        'serie',
        'number',
        'pin',
        'amount',
        'aid_id',
        'issuer',
        'exp',
        'delivery_date',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'delivery_date' => 'date',
    ];

    // Relación opcional con Aid
    public function aid():BelongsTo
    {
        return $this->belongsTo(Aid::class);
    }

    // Relación con Beneficiary a través de Aid (si `aid_id` no es null)
    public function beneficiary():HasOneThrough
    {
        return $this->hasOneThrough(Beneficiary::class, Aid::class, 'id', 'id', 'aid_id', 'beneficiary_id');
    }
}