<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('producto_compuesto', function (Blueprint $table) {
            $table->id();
            $table->foreignId('producto_id')
                ->constrained()
                ->onDelete('no action')
                ->onUpdate('no action');
            $table->foreignId('componente_id')
                ->references('id')
                ->on('productos')
                ->onDelete('no action')
                ->onUpdate('no action');
            $table->decimal('cantidad', 10, 2);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('producto_compuesto');
    }
};