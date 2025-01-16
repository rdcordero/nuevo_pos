<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained()->onDelete('NO ACTION');
            $table->foreignId('pais_id')->constrained('paises')->onDelete('NO ACTION');
            
            // Datos de identificación
            $table->string('codigo', 20)->nullable();
            $table->string('nombre');
            $table->string('nombre_comercial')->nullable();
            $table->string('dui', 10)->nullable();
            $table->string('nit', 17)->nullable();
            $table->string('nrc', 8)->nullable();
            $table->enum('tipo_cliente', ['contribuyente', 'no_contribuyente']);
            $table->string('giro')->nullable();
            $table->string('actividad_economica_codigo',10)->nullable();
            
            // Dirección
            $table->string('direccion');
            $table->string('departamento', 50)->nullable();
            $table->string('municipio', 100)->nullable();
            $table->string('distrito', 100)->nullable();
            $table->string('complemento')->nullable();
            
            // Contacto
            $table->string('telefono', 20)->nullable();
            $table->string('celular', 20)->nullable();
            $table->string('email')->nullable();
            $table->string('web')->nullable();
            
            // Datos comerciales
            $table->enum('categoria', ['normal', 'frecuente', 'vip'])->default('normal');
            $table->decimal('limite_credito', 10, 2)->default(0);
            $table->integer('dias_credito')->default(0);
            $table->string('vendedor')->nullable();
            $table->text('observaciones')->nullable();
            
            // Control
            $table->boolean('exento')->default(false);
            $table->boolean('gran_contribuyente')->default(false);
            $table->boolean('activo')->default(true);
            $table->timestamps();
            $table->softDeletes();

            // Índices
            $table->unique(['empresa_id', 'nit']);
            $table->unique(['empresa_id', 'nrc']);
            $table->index('nombre');
            $table->index('tipo_cliente');
            
            // Restricción de llave foránea compuesta para actividad económica
            $table->foreign('actividad_economica_codigo')
                  ->references( 'codigo')
                  ->on('actividades_economicas')
                  ->onDelete('NO ACTION');
        });
    }

    public function down()
    {
        Schema::dropIfExists('clientes');
    }
};

