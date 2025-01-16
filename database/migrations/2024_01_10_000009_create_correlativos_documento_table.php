<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('correlativos_documento', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained()->onDelete('NO ACTION');
            $table->foreignId('sucursal_id')->constrained('sucursales')->onDelete('NO ACTION');
            $table->foreignId('tipo_documento_id')->constrained('tipos_documento_venta')->onDelete('NO ACTION');
            $table->string('serie', 20)->nullable();
            $table->integer('correlativo_actual')->default(0);
            $table->integer('correlativo_inicial');
            $table->integer('correlativo_final');
            $table->date('fecha_inicio');
            $table->date('fecha_vencimiento');
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->unique(['sucursal_id', 'tipo_documento_id', 'serie'], 'correlativo_unique_idx');
        });
    }

    public function down()
    {
        Schema::dropIfExists('correlativos_documento');
    }
};

