<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Certificate extends Model
{
    protected $fillable = [
        'recipient_type',
        'recipient_name',
        'beneficiary_id',
        'family_id',
        'title',
        'body',
    ];

    // relaciones
    public function beneficiary(): BelongsTo
    {
        return $this->belongsTo(Beneficiary::class);
    }

    public function family(): BelongsTo
    {
        return $this->belongsTo(Family::class);
    }

    /**
     * @return array<string, string>
     */
    public static function recipientTypes(): array
    {
        return [
            'beneficiario' => 'Beneficiario',
            'familiar' => 'Familiar',
            'externo' => 'Otro (no está en la base de datos)',
        ];
    }

    /**
     * Deja `recipient_name` con el nombre de la persona elegida y limpia la
     * relación que no corresponde al tipo.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public static function withResolvedRecipient(array $data): array
    {
        if (($data['recipient_type'] ?? null) === 'beneficiario') {
            $data['recipient_name'] = Beneficiary::query()->find($data['beneficiary_id'] ?? null)?->name
                ?? $data['recipient_name'];
            $data['family_id'] = null;
        } elseif (($data['recipient_type'] ?? null) === 'familiar') {
            $data['recipient_name'] = Family::query()->find($data['family_id'] ?? null)?->name
                ?? $data['recipient_name'];
            $data['beneficiary_id'] = null;
        } else {
            $data['beneficiary_id'] = null;
            $data['family_id'] = null;
        }

        return $data;
    }
}
