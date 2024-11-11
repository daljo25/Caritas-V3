<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Appointment extends Model
{
    public function Beneficiary():BelongsTo{
        return $this->belongsTo(Beneficiary::class);
    }
}
