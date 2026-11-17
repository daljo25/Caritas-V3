<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class WorkExperience extends Model
{
    protected $fillable = [
        'name',
    ];

    // relaciones
    public function beneficiaries(): MorphToMany
    {
        return $this->morphedByMany(Beneficiary::class, 'person', 'person_work_experiences');
    }

    public function families(): MorphToMany
    {
        return $this->morphedByMany(Family::class, 'person', 'person_work_experiences');
    }
}
