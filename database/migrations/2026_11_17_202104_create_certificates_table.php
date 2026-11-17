<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Documentos redactados a mano (certificados, solicitudes, autorizaciones).
     * El destinatario puede ser un beneficiario, un familiar o un nombre suelto.
     */
    public function up(): void
    {
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->string('recipient_type')->default('externo');
            // Copia del nombre, para que el listado y la búsqueda no dependan de
            // que la persona siga existiendo.
            $table->string('recipient_name');
            // Documento de identidad, también copiado para que el impreso no
            // dependa de la ficha de la persona.
            $table->string('recipient_document')->nullable();
            $table->foreignId('beneficiary_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('family_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->text('body');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certificates');
    }
};
