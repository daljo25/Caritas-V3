<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Family extends Model
{
    //relaciones
    public function Beneficiary():BelongsTo{
        return $this->belongsTo(Beneficiary::class);
    }

    //cast
    protected $casts = [
        'cdp_state' => 'boolean',
        'sepe_state' => 'boolean',
        'rmv_state' => 'boolean',
        'remisa_state' => 'boolean',
    ];
}
