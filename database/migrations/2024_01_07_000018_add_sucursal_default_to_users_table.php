<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('sucursal_default_id')
                  ->nullable()
                  ->constrained('sucursales')
                  ->noActionOnDelete()
                  ->noActionOnUpdate();
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['sucursal_default_id']);
            $table->dropColumn('sucursal_default_id');
        });
    }
};