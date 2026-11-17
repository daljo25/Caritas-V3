<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Detalle libre de las ayudas de "otras necesidades", que el recibo añade
     * entre paréntesis al concepto marcado.
     */
    public function up(): void
    {
        Schema::table('aids', function (Blueprint $table) {
            $table->string('type_detail')->nullable()->after('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('aids', function (Blueprint $table) {
            $table->dropColumn('type_detail');
        });
    }
};
