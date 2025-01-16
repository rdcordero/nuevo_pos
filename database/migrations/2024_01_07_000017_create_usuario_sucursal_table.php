<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('usuario_sucursal', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('sucursal_id')->constrained('sucursales')->onDelete('cascade');
            $table->boolean('activo')->default(true);
            $table->timestamps();

            // Un usuario solo puede estar asignado una vez a una sucursal
            $table->unique(['user_id', 'sucursal_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('usuario_sucursal');
    }
};