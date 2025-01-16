<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('transferencias', function (Blueprint $table) {
            $table->id();
            $table->string('numero_documento');
            $table->foreignId('empresa_id')->constrained()->onDelete('NO ACTION');
            $table->foreignId('bodega_origen_id')->constrained('bodegas')->onDelete('NO ACTION');
            $table->foreignId('bodega_destino_id')->constrained('bodegas')->onDelete('NO ACTION');
            $table->date('fecha');
            $table->text('motivo');
            $table->text('observacion')->nullable();
            $table->enum('estado', ['pendiente', 'completada', 'cancelada'])->default('pendiente');
            $table->foreignId('usuario_id')->constrained('users')->onDelete('NO ACTION');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('transferencias');
    }
};

