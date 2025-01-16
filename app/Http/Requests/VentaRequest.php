<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VentaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'cliente_id' => 'required|exists:clientes,id',
            'tipo_documento_id' => 'required|exists:tipos_documento_venta,id',
            'serie' => 'nullable|string|max:10',
            'numero' => 'required|string|max:20',
            'fecha' => 'required|date',
            'fecha_vencimiento' => 'nullable|date|after_or_equal:fecha',
            'moneda' => 'required|string|size:3',
            'tasa_cambio' => 'required|numeric|min:0',
            'condiciones_pago' => 'nullable|string|max:255',
            'notas' => 'nullable|string',
            'referencia' => 'nullable|string|max:100',
            
            // Detalles de la venta
            'detalles' => 'required|array|min:1',
            'detalles.*.producto_id' => 'required|exists:productos,id',
            'detalles.*.cantidad' => 'required|numeric|min:0.01',
            'detalles.*.precio_unitario' => 'required|numeric|min:0',
            'detalles.*.descuento' => 'nullable|numeric|min:0',
            'detalles.*.notas' => 'nullable|string',

            // Pagos
            'pagos' => 'required|array|min:1',
            'pagos.*.forma_pago_id' => 'required|exists:formas_pago,id',
            'pagos.*.monto' => 'required|numeric|min:0.01',
            'pagos.*.referencia' => 'nullable|string|max:100',
            'pagos.*.notas' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'cliente_id.required' => 'Debe seleccionar un cliente',
            'cliente_id.exists' => 'El cliente seleccionado no es válido',
            'tipo_documento_id.required' => 'Debe seleccionar un tipo de documento',
            'tipo_documento_id.exists' => 'El tipo de documento seleccionado no es válido',
            'serie.nullable' => 'La serie es opcional',
            'numero.required' => 'El número es obligatorio',
            'numero.max' => 'El número no puede tener más de 20 caracteres',
            'fecha.required' => 'La fecha es obligatoria',
            'fecha.date' => 'La fecha no es válida',
            'fecha_vencimiento.date' => 'La fecha de vencimiento no es válida',
            'fecha_vencimiento.after_or_equal' => 'La fecha de vencimiento debe ser posterior o igual a la fecha de la venta',
            'moneda.required' => 'La moneda es obligatoria',
            'moneda.size' => 'La moneda debe tener 3 caracteres',
            'tasa_cambio.required' => 'La tasa de cambio es obligatoria',
            'tasa_cambio.numeric' => 'La tasa de cambio debe ser un número',
            'tasa_cambio.min' => 'La tasa de cambio no puede ser negativa',
            
            'detalles.required' => 'Debe agregar al menos un producto',
            'detalles.min' => 'Debe agregar al menos un producto',
            'detalles.*.producto_id.required' => 'Debe seleccionar un producto',
            'detalles.*.producto_id.exists' => 'El producto seleccionado no es válido',
            'detalles.*.cantidad.required' => 'La cantidad es obligatoria',
            'detalles.*.cantidad.numeric' => 'La cantidad debe ser un número',
            'detalles.*.cantidad.min' => 'La cantidad debe ser mayor a 0',
            'detalles.*.precio_unitario.required' => 'El precio unitario es obligatorio',
            'detalles.*.precio_unitario.numeric' => 'El precio unitario debe ser un número',
            'detalles.*.precio_unitario.min' => 'El precio unitario no puede ser negativo',
            'detalles.*.descuento.numeric' => 'El descuento debe ser un número',
            'detalles.*.descuento.min' => 'El descuento no puede ser negativo',

            'pagos.required' => 'Debe agregar al menos un pago',
            'pagos.min' => 'Debe agregar al menos un pago',
            'pagos.*.forma_pago_id.required' => 'Debe seleccionar una forma de pago',
            'pagos.*.forma_pago_id.exists' => 'La forma de pago seleccionada no es válida',
            'pagos.*.monto.required' => 'El monto es obligatorio',
            'pagos.*.monto.numeric' => 'El monto debe ser un número',
            'pagos.*.monto.min' => 'El monto debe ser mayor a 0',
        ];
    }
}

