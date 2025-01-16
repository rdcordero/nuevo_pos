<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Tabla principal de ventas
        Schema::create('ventas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas');
            $table->foreignId('sucursal_id')->constrained('sucursales');
            $table->foreignId('cliente_id')->constrained('clientes');
            $table->foreignId('usuario_id')->constrained('users');
            $table->foreignId('tipo_documento_id')->constrained('tipos_documento_venta');
            $table->string('serie', 10)->nullable();
            $table->string('numero', 20);
            $table->dateTime('fecha');
            $table->dateTime('fecha_vencimiento')->nullable();
            $table->string('moneda', 3)->default('USD');
            $table->decimal('tasa_cambio', 10, 4)->default(1.0000);
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('descuento', 10, 2)->default(0);
            $table->decimal('impuesto', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);
            $table->enum('estado', ['pendiente', 'completada', 'anulada'])->default('pendiente');
            $table->text('notas')->nullable();
            $table->string('condiciones_pago')->nullable();
            $table->string('referencia')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Índices
            $table->index(['empresa_id', 'sucursal_id', 'fecha']);
            $table->index(['tipo_documento_id', 'serie', 'numero']); //Updated index
            $table->index('estado');
            $table->unique(['empresa_id', 'tipo_documento_id', 'serie', 'numero']); //Updated unique constraint

        });

        // Tabla de detalles de venta
        Schema::create('venta_detalles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('venta_id')->constrained('ventas')->onDelete('cascade');
            $table->foreignId('producto_id')->constrained('productos');
            $table->string('codigo', 50);
            $table->string('descripcion');
            $table->decimal('cantidad', 10, 2);
            $table->decimal('precio_unitario', 10, 4);
            $table->decimal('descuento', 10, 2)->default(0);
            $table->decimal('impuesto', 10, 2)->default(0);
            $table->decimal('subtotal', 10, 2);
            $table->decimal('total', 10, 2);
            $table->text('notas')->nullable();
            $table->timestamps();

            // Índices
            $table->index('venta_id');
            $table->index('producto_id');
        });

        // Tabla de pagos de venta
        Schema::create('venta_pagos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('venta_id')->constrained('ventas')->onDelete('cascade');
            $table->foreignId('forma_pago_id')->constrained('formas_pago');
            $table->dateTime('fecha');
            $table->decimal('monto', 10, 2);
            $table->string('referencia')->nullable();
            $table->text('notas')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Índices
            $table->index('venta_id');
            $table->index('forma_pago_id');
            $table->index('fecha');
        });

        // Tabla de impuestos de venta
        Schema::create('venta_impuestos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('venta_id')->constrained('ventas')->onDelete('cascade');
            $table->foreignId('impuesto_id')->constrained('impuestos');
            $table->decimal('base_imponible', 10, 2);
            $table->decimal('porcentaje', 5, 2);
            $table->decimal('monto', 10, 2);
            $table->timestamps();

            // Índices
            $table->index('venta_id');
            $table->index('impuesto_id');
        });

        // Tabla de descuentos de venta
        Schema::create('venta_descuentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('venta_id')->constrained('ventas')->onDelete('cascade');
            $table->string('tipo', 20); // porcentaje, monto
            $table->string('descripcion');
            $table->decimal('porcentaje', 5, 2)->nullable();
            $table->decimal('monto', 10, 2);
            $table->timestamps();

            // Índices
            $table->index('venta_id');
        });

        // Tabla de historial de estados de venta
        Schema::create('venta_estados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('venta_id')->constrained('ventas')->onDelete('cascade');
            $table->foreignId('usuario_id')->constrained('users');
            $table->string('estado_anterior');
            $table->string('estado_nuevo');
            $table->text('observacion')->nullable();
            $table->timestamps();

            // Índices
            $table->index('venta_id');
            $table->index('usuario_id');
        });
    }

    public function down()
    {
        // Eliminar las tablas en orden inverso debido a las restricciones de clave foránea
        Schema::dropIfExists('venta_estados');
        Schema::dropIfExists('venta_descuentos');
        Schema::dropIfExists('venta_impuestos');
        Schema::dropIfExists('venta_pagos');
        Schema::dropIfExists('venta_detalles');
        Schema::dropIfExists('ventas');
    }
};

