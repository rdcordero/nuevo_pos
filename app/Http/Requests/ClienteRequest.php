<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClienteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $rules = [
            'nombre' => 'required|string|max:255',
            'nombre_comercial' => 'nullable|string|max:255',
            'tipo_cliente' => 'required|in:contribuyente,no_contribuyente',
            'nit' => 'nullable|string|max:17',
            'nrc' => 'nullable|string|max:8',
            'dui' => 'nullable|string|max:10',
            'pais_id' => 'required|string|exists:paises,codigo',
            'direccion' => 'required|string|max:255',
            'departamento' => 'nullable|string|max:100',
            'municipio' => 'nullable|string|max:100',
            'distrito' => 'nullable|string|max:100',
            'complemento' => 'nullable|string|max:255',
            'telefono' => 'nullable|string|max:20',
            'celular' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'web' => 'nullable|url|max:255',
            'categoria' => 'required|in:normal,frecuente,vip',
            'limite_credito' => 'required|numeric|min:0',
            'dias_credito' => 'required|integer|min:0',
            'vendedor' => 'nullable|string|max:255',
            'observaciones' => 'nullable|string',
            'exento' => 'boolean',
            'gran_contribuyente' => 'boolean',
            'activo' => 'boolean',
        ];

        // Agregar reglas adicionales si es contribuyente
        if ($this->input('tipo_cliente') === 'contribuyente') {
            $rules['nrc'] = 'required|string|max:8';
            $rules['actividad_economica_codigo'] = 'required|string|exists:actividades_economicas,codigo';
        }

        return $rules;
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Validar que el NRC tenga el formato correcto cuando es contribuyente
            if ($this->input('tipo_cliente') === 'contribuyente' && $this->input('nrc')) {
                if (!preg_match('/^\d{6}-\d$/', $this->input('nrc'))) {
                    $validator->errors()->add('nrc', 'El NRC debe tener el formato: 123456-7');
                }
            }

            // Validar formato de NIT si se proporciona
            if ($this->input('nit')) {
                if (!preg_match('/^\d{4}-\d{6}-\d{3}-\d$/', $this->input('nit'))) {
                    $validator->errors()->add('nit', 'El NIT debe tener el formato: 0614-123456-123-1');
                }
            }

            // Validar formato de DUI si se proporciona
            if ($this->input('dui')) {
                if (!preg_match('/^\d{8}-\d$/', $this->input('dui'))) {
                    $validator->errors()->add('dui', 'El DUI debe tener el formato: 12345678-9');
                }
            }
        });
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre es obligatorio',
            'tipo_cliente.required' => 'El tipo de cliente es obligatorio',
            'tipo_cliente.in' => 'El tipo de cliente seleccionado no es válido',
            'pais_id.required' => 'El país es obligatorio',
            'pais_id.exists' => 'El país seleccionado no es válido',
            'direccion.required' => 'La dirección es obligatoria',
            'categoria.required' => 'La categoría es obligatoria',
            'categoria.in' => 'La categoría seleccionada no es válida',
            'limite_credito.required' => 'El límite de crédito es obligatorio',
            'limite_credito.numeric' => 'El límite de crédito debe ser un número',
            'limite_credito.min' => 'El límite de crédito no puede ser negativo',
            'dias_credito.required' => 'Los días de crédito son obligatorios',
            'dias_credito.integer' => 'Los días de crédito deben ser un número entero',
            'dias_credito.min' => 'Los días de crédito no pueden ser negativos',
            'email.email' => 'El correo electrónico no tiene un formato válido',
            'web.url' => 'El sitio web debe ser una URL válida',
            'nrc.required' => 'El NRC es obligatorio para contribuyentes',
            'actividad_economica_codigo.required' => 'La actividad económica es obligatoria para contribuyentes',
            'actividad_economica_codigo.exists' => 'La actividad económica seleccionada no es válida',
        ];
    }
}

