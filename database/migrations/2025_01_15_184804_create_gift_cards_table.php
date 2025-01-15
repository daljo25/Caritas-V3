<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('gift_cards', function (Blueprint $table) {
            $table->id();
            $table->string('serie', 50); // SERIE
            $table->string('number', 50); // Nº
            $table->string('pin', 50); // PIN
            $table->decimal('amount', 10, 2); // €
            $table->foreignId('aid_id')->nullable()->constrained('aids')->nullOnDelete(); // Relación opcional con ayudas
            $table->string('issuer')->nullable(); // Emisor
            $table->string('exp', 20)->nullable(); // EXP
            $table->date('delivery_date')->nullable(); // FECHA ENTREGA
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gift_cards');
    }
};
