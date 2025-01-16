<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('usuario_empresa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('empresa_id')->constrained('empresas')->onDelete('cascade');
            $table->boolean('activo')->default(true);
            $table->timestamps();

            // Un usuario solo puede estar asignado una vez a una empresa
            $table->unique(['user_id', 'empresa_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('usuario_empresa');
    }
};