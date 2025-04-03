<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Beneficiary extends Model
{
    //relaciones
    public function Volunteer():BelongsTo{
        return $this->belongsTo(Volunteer::class);
    }
    public function Family():HasMany{
        return $this->hasMany(Family::class);
    }
    public function Record():HasMany{
        return $this->hasMany(Record::class);
    }
    public function Derivations():HasMany{
        return $this->hasMany(Derivation::class);
    }
    public function Aids():HasMany{
        return $this->hasMany(Aid::class);
    }
    public function Appointments():HasMany{
        return $this->hasMany(Appointment::class);
    }
    public function Attendances():HasMany{
        return $this->hasMany(Attendance::class);
    }


    //cast
    protected $casts = [
        'family_book' => 'boolean',
        'rent_contract' => 'boolean',
        'cdp_state' => 'boolean',
        'sepe_state' => 'boolean',
        'rmv_state' => 'boolean',
        'remisa_state' => 'boolean',

    ];
}
