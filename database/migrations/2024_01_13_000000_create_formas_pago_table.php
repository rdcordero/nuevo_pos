<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('formas_pago', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas');
            $table->string('nombre');
            $table->string('codigo', 20)->unique();
            $table->enum('tipo', ['efectivo', 'tarjeta', 'transferencia', 'cheque', 'otro']);
            $table->boolean('requiere_referencia')->default(false);
            $table->boolean('activo')->default(true);
            $table->text('descripcion')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Índices
            $table->index('empresa_id');
            $table->index('tipo');
            $table->index('activo');
        });
    }

    public function down()
    {
        Schema::dropIfExists('formas_pago');
    }
};

