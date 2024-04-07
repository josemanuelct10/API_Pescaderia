<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MariscoRequest extends FormRequest
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
            'nombre' => ['required', 'string', 'max:50', 'regex:/^[a-zA-ZáéíóúÁÉÍÓÚ\s]+$/'],
            'descripcion' => ['nullable', 'string', 'max:200'],
            'origen' => ['required', 'string', 'max:50', 'regex:/^[a-zA-ZáéíóúÁÉÍÓÚ\s]+$/'],
            'precioKG' => ['required', 'numeric', 'gt:0'],
            'cantidad' => ['required', 'numeric', 'gt:0'],
            'fechaCompra' => ['required', 'date', 'after_or_equal:today'],
            'categoria' => ['required', 'string', 'max:50', 'regex:/^[a-zA-ZáéíóúÁÉÍÓÚ\s]+$/'],
            'imagen' => ['nullable', 'image', 'mimes:jpg,png']
        ];
    }
}
