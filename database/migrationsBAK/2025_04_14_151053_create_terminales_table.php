<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('Terminales', function (Blueprint $table) {
            $table->increments('TerminalesId'); 
            $table->integer('IdUbicacionesFisicas');
            $table->string('Nombre');
            $table->ipAddress('IpAddress');
            $table->string('IpV6')->nullable();
            $table->string('MacAddress');
            $table->integer('Estado')->default(1);  
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('Terminales');
    }
};
