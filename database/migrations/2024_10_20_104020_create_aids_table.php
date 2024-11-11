<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * campos de la tabla ayudas.
     * tipo de ayuda, estado, fecha inicio, fecha final, monto aprovado, monto total, notas
     * beneficiario id, voluntario id, colaborador id
     */
    public function up(): void
    {
        Schema::create('aids', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->string('status')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('paid_by')->nullable();
            $table->decimal('approved_amount', 10, 2)->nullable();
            $table->decimal('total_amount', 10, 2)->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('beneficiary_id')->constrained('beneficiaries')->onDelete('cascade');
            $table->foreignId('volunteer_id')->nullable()->constrained('volunteers')->onDelete('set null');
            $table->foreignId('collaborator_id')->nullable()->constrained('collaborators')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aids');
    }
};
