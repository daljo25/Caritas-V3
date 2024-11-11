<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Volunteer extends Model
{
    //relaciones
    public function Beneficiaries():HasMany{
        return $this->hasMany(Beneficiary::class);
    }
    public function Records():HasMany{
        return $this->hasMany(Record::class);
    }
    public function Derivations():HasMany{
        return $this->hasMany(Derivation::class);
    }
    public function Aids():HasMany{
        return $this->hasMany(Aid::class);
    }
}
