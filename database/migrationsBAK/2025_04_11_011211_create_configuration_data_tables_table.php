<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Ejecuta las migraciones.
     */
    public function up(): void
    { 
        Schema::create('configuracion_tablas_datos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('tabla');
            $table->json('columnas_visibles');
            $table->smallInteger('registros_por_pagina')->default(50);

            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Revierte las migraciones.
     */
    public function down(): void
    {
        Schema::dropIfExists('configuracion_tablas_datos');
    }
};
