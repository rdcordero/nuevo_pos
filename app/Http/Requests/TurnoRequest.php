<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TurnoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'sucursal_id' => 'required|exists:sucursales,id',
            'caja_id' => 'required|exists:cajas,id',
            'monto_apertura' => 'required|numeric|min:0',
            'observaciones_apertura' => 'nullable|string|max:500'
        ];

        // Si es una actualización
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules['monto_apertura'] = 'sometimes|required|numeric|min:0';
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'sucursal_id.required' => 'La sucursal es obligatoria',
            'sucursal_id.exists' => 'La sucursal seleccionada no es válida',
            'caja_id.required' => 'La caja es obligatoria',
            'caja_id.exists' => 'La caja seleccionada no es válida',
            'monto_apertura.required' => 'El monto de apertura es obligatorio',
            'monto_apertura.numeric' => 'El monto de apertura debe ser un número',
            'monto_apertura.min' => 'El monto de apertura no puede ser negativo',
            'observaciones_apertura.max' => 'Las observaciones no pueden tener más de 500 caracteres'
        ];
    }
}

