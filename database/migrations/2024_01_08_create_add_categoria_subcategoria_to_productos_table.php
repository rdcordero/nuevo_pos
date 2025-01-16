<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->foreignId('categoria_id')
                ->after('empresa_id')
                ->constrained('categorias')
                ;

           
        });
    }

    public function down()
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->dropForeign(['subcategoria_id']);
            $table->dropForeign(['categoria_id']);
            $table->dropColumn(['categoria_id', 'subcategoria_id']);
        });
    }
};