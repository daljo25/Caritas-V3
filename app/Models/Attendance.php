<?php

namespace App\Models;

use App\Models\Beneficiary;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    protected $fillable = [
        'certificate_number',
        'beneficiary_id',
        'attendance_date',
        'attendance_time',
        'purpose'
    ];

    public function Beneficiary():BelongsTo{
        return $this->belongsTo(Beneficiary::class);
    }


    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $year = now()->year;
            $lastCertificate = static::whereYear('created_at', $year)
                ->orderBy('id', 'desc')
                ->first();

            $sequence = $lastCertificate 
                ? (int) substr($lastCertificate->certificate_number, -4) + 1
                : 1;

            $model->certificate_number = sprintf('CERT-%d-%04d', $year, $sequence);
        });
    }
}