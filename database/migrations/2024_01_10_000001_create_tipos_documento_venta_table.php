<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tipos_documento_venta', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 10)->unique();
            $table->string('nombre');
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        // Insertar tipos de documento básicos
        DB::table('tipos_documento_venta')->insert([
            [
                'codigo' => 'FCF',
                'nombre' => 'Factura Consumidor Final',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'codigo' => 'CCF',
                'nombre' => 'Comprobante de Crédito Fiscal',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'codigo' => 'FEX',
                'nombre' => 'Factura de Exportación',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('tipos_documento_venta');
    }
};

