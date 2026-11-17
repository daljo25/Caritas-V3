<?php

namespace App\Models;

use Filament\Forms\Components\RichEditor\RichContentRenderer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Certificate extends Model
{
    protected $fillable = [
        'recipient_type',
        'recipient_name',
        'recipient_document',
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
     * El cuerpo se escribe con el editor enriquecido, así que se guarda como
     * HTML. Se sanea antes de imprimirlo y, si algún registro llega en texto
     * plano, se respetan sus saltos de línea.
     */
    public function renderedBody(): string
    {
        if (blank($this->body)) {
            return '';
        }

        if (! str_contains($this->body, '<')) {
            return nl2br(e($this->body));
        }

        return RichContentRenderer::make($this->body)->toHtml();
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
     * Deja `recipient_name` y `recipient_document` con los datos de la persona
     * elegida y limpia la relación que no corresponde al tipo.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public static function withResolvedRecipient(array $data): array
    {
        $type = $data['recipient_type'] ?? null;

        $person = match ($type) {
            'beneficiario' => Beneficiary::query()->find($data['beneficiary_id'] ?? null),
            'familiar' => Family::query()->find($data['family_id'] ?? null),
            // El destinatario externo escribe nombre y documento a mano.
            default => null,
        };

        if ($person instanceof Beneficiary || $person instanceof Family) {
            $data['recipient_name'] = $person->name;
            $data['recipient_document'] = $person->dni;
        }

        $data['beneficiary_id'] = $type === 'beneficiario' ? ($data['beneficiary_id'] ?? null) : null;
        $data['family_id'] = $type === 'familiar' ? ($data['family_id'] ?? null) : null;

        return $data;
    }
}
