<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Personas (beneficiarios y familiares) que tienen una experiencia laboral.
     * Es polimórfico para que el mismo catálogo sirva a cualquier modelo con
     * `workExperiences()`.
     */
    public function up(): void
    {
        Schema::create('person_work_experiences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('work_experience_id')->constrained()->cascadeOnDelete();
            $table->morphs('person');

            $table->unique(['work_experience_id', 'person_type', 'person_id'], 'person_work_experience_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('person_work_experiences');
    }
};
