<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('codigos_barra', function (Blueprint $table) {
            $table->id();
            $table->foreignId('producto_id')->constrained('productos')->onDelete('cascade');
            $table->string('codigo')->unique();
            $table->boolean('principal')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('codigos_barra');
    }
};