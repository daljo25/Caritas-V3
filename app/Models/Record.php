<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Record extends Model
{
    //relaciones
    public function Beneficiary():BelongsTo{
        return $this->belongsTo(Beneficiary::class);
    }
    public function Volunteer():BelongsTo{
        return $this->belongsTo(Volunteer::class);
    }
}
