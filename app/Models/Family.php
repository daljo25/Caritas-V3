<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Family extends Model
{
    // relaciones
    public function Beneficiary(): BelongsTo
    {
        return $this->belongsTo(Beneficiary::class);
    }

    public function workExperiences(): MorphToMany
    {
        return $this->morphToMany(WorkExperience::class, 'person', 'person_work_experiences');
    }

    // cast
    protected $casts = [
        'cdp_state' => 'boolean',
        'sepe_state' => 'boolean',
        'rmv_state' => 'boolean',
        'remisa_state' => 'boolean',
    ];
}
