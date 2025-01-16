<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('ajustes_inventario', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained()->onDelete('NO ACTION');
            $table->foreignId('sucursal_id')->constrained('sucursales')->onDelete('NO ACTION');
            $table->string('numero_documento');
            $table->enum('tipo', ['entrada', 'salida']);
            $table->date('fecha');
            $table->text('motivo');
            $table->text('observacion')->nullable();
            $table->foreignId('usuario_id')->constrained('users')->onDelete('NO ACTION');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('ajustes_inventario');
    }
};

