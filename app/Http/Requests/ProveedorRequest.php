<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProveedorRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:100', 'regex:/^[^\d\W_]+(?:\s+[^\d\W_]+)*$/u'],
            'direccion' => ['required', 'string', 'max:200'],
            'telefono' => ['required', 'string', 'size:9', 'regex:/^[69]/'],
            'categoria' => ['required', 'string', 'max:100'],
            'cif' => ['required', 'string', 'size:9', 'regex:/^[A-Z]{1}[0-9]{8}$/'],
        ];
    }
}
