<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('subcategorias', function (Blueprint $table) {
            $table->foreignId('empresa_id')
                  ->after('id')
                  ->constrained('empresas')
                  ->noActionOnDelete()
                  ->noActionOnUpdate();
        });
    }

    public function down()
    {
        Schema::table('subcategorias', function (Blueprint $table) {
            $table->dropForeign(['empresa_id']);
            $table->dropColumn('empresa_id');
        });
    }
};