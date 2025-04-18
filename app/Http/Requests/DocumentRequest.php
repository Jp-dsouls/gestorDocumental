<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DocumentRequest extends FormRequest
{
    /**
     * Determinar si el usuario está autorizado para realizar esta solicitud.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Obtener las reglas de validación que se aplican a la solicitud.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules()
    {
        $rules = [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
        ];

        // Si es una solicitud de creación o si se está actualizando el archivo
        if ($this->isMethod('post') || $this->hasFile('document')) {
            $rules['document'] = 'required|file|max:10240'; // 10MB máximo
        }

        return $rules;
    }

    /**
     * Obtener los mensajes de error personalizados para las reglas de validación.
     *
     * @return array<string, string>
     */
    public function messages()
    {
        return [
            'title.required' => 'El título es obligatorio.',
            'title.max' => 'El título no puede tener más de 255 caracteres.',
            'category_id.required' => 'La categoría es obligatoria.',
            'category_id.exists' => 'La categoría seleccionada no existe.',
            'document.required' => 'El archivo es obligatorio.',
            'document.file' => 'Debe ser un archivo válido.',
            'document.max' => 'El archivo no puede pesar más de 10MB.',
        ];
    }
} 