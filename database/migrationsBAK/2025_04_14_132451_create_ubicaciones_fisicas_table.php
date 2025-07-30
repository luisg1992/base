<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('UbicacionFisica', function (Blueprint $table) {
            $table->increments('IdUbicacionFisica');
            $table->string('Nombre');
            $table->integer('Estado')->default(1); 
            $table->string('TipoUbicacionFisica');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('UbicacionFisica');
    }
};
