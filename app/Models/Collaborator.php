<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Collaborator extends Model
{
    //relaciones
    public function Derivations():HasMany{
        return $this->hasMany(Derivation::class);
    }
    public function Aids():HasMany{
        return $this->hasMany(Aid::class);
    }
}
