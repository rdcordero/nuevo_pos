<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::create('paises', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 3)->unique();
            $table->string('nombre');
            $table->string('codigo_mh', 3)->nullable()->unique();
            $table->timestamps();
        });

        // Insertar El Salvador como país por defecto
        DB::table('paises')->insert([
            'codigo' => 'SLV',
            'nombre' => 'El Salvador',
            'codigo_mh' => '222',
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('paises');
    }
};

