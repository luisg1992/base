<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('Impresoras', function (Blueprint $table) {
            $table->increments('ImpresorasId');  
            $table->integer('IdTerminales');
            $table->string('Nombre');
            $table->integer('Estado')->default(1);
            $table->integer('PorDefecto')->default(1);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('Impresoras');
    }
};
