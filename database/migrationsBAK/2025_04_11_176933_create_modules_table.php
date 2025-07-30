<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('Modulos', function (Blueprint $table) {
            $table->id('ModuloId'); // Usamos id() para crear la clave primaria ModuloId
            $table->unsignedBigInteger('ModuloPadreId')->nullable(); // Clave foránea que referencia a ModuloId

            // Definimos los campos solicitados
            $table->string('Etiqueta', 100); // NOMBRE
            $table->string('Subtitulo', 100); // Subtitulo
            $table->string('Descripcion')->nullable(); // Descripción
            $table->string('Icono')->nullable(); // Icono
            $table->string('Url')->nullable(); // URL

            $table->integer('EsAccesoDirecto')->default(0); // Acceso directo
            $table->integer('EstaBloqueado')->default(0); // Estado de bloqueo
            $table->integer('Estado')->default(1); // Estado
            $table->integer('Orden')->default(0); // Orden

            // Definimos la relación de clave foránea (auto referencia a la misma tabla)
            $table->foreign('ModuloPadreId')->references('ModuloId')->on('Modulos');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        // Eliminamos la clave foránea antes de eliminar la tabla
        Schema::table('Modulos', function (Blueprint $table) {
            $table->dropForeign(['ModuloPadreId']);
        });

        // Eliminamos la tabla
        Schema::dropIfExists('Modulos');
    }
};
