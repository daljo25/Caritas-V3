<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Donor extends Model
{
    public function Donation():HasMany{
        return $this->hasMany(Donation::class);
    }
}
