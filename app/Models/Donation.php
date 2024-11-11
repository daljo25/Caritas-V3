<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Donation extends Model
{
    public function Donor():BelongsTo{
        return $this->belongsTo(Donor::class);
    }
}
