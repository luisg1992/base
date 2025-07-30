<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sesiones_login', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade'); // Permitir nulos para 'user_id'
            $table->timestamp('hora_inicio_sesion'); // Hora en que el usuario inició sesión
            $table->timestamp('hora_cierre_sesion')->nullable(); // Hora en que el usuario cerró sesión
            $table->timestamp('ultimo_intento_at')->nullable(); // Hora del último intento fallido de inicio de sesión
            $table->string('razon')->nullable(); // Razón del fallo (por ejemplo: "contraseña incorrecta")
            $table->ipAddress('direccion_ip')->nullable(); // Dirección IP desde donde se realizó el intento
            $table->text('agente_usuario')->nullable(); // Información del navegador o dispositivo 
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sesiones_login');
    }
};
