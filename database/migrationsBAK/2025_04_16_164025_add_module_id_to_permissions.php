<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('permissions', function (Blueprint $table) {
            $table->unsignedBigInteger('modulo_id')->nullable()->after('guard_name');
            $table->string('descripcion')->nullable()->after('modulo_id');

            // ✅ Relación a la tabla Modulos
            $table->foreign('modulo_id')->references('ModuloId')->on('Modulos');
        });
    }

    public function down(): void
    {
        Schema::table('permissions', function (Blueprint $table) {
            $table->dropForeign(['modulo_id']);
            $table->dropColumn(['modulo_id', 'descripcion']);
        });
    }
};
