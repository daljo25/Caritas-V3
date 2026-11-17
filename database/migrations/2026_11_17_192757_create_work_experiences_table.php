<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Catálogo de experiencias laborales que alimenta el select de personas.
     *
     * Las 20 iniciales salen de los oficios que ya aparecen en `education` de
     * beneficiarios y familiares. Van en la migración porque el despliegue solo
     * corre `migrate --force`, y así producción arranca con el catálogo servido.
     */
    public function up(): void
    {
        Schema::create('work_experiences', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        $now = now();

        DB::table('work_experiences')->insert(array_map(
            fn (string $name): array => ['name' => $name, 'created_at' => $now, 'updated_at' => $now],
            [
                'Limpieza',
                'Cuidados de personas',
                'Construcción y albañilería',
                'Hostelería y camarero/a',
                'Enfermería y auxiliares',
                'Administración',
                'Cocina',
                'Medicina y sanidad',
                'Informática',
                'Ingeniería',
                'Jardinería y agricultura',
                'Comercio y ventas',
                'Peluquería y estética',
                'Pintura',
                'Electricidad',
                'Almacén y carretillero',
                'Mecánica',
                'Conducción',
                'Seguridad y vigilancia',
                'Cristalería',
                'Carpintería',
            ],
        ));
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_experiences');
    }
};
