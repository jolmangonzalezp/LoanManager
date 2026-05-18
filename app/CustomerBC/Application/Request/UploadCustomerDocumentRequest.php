<?php

declare(strict_types=1);

namespace App\CustomerBC\Application\Request;

use App\CustomerBC\Domain\ValueObject\CustomerDocumentType;
use Illuminate\Foundation\Http\FormRequest;

class UploadCustomerDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'file' => ['required', 'file', 'max:5120', 'mimes:jpg,jpeg,png,webp,pdf'],
            'type' => ['required', 'string', 'in:' . implode(',', CustomerDocumentType::values())],
            'side' => ['nullable', 'string', 'in:front,back'],
        ];
    }

    public function messages(): array
    {
        return [
            'file.required' => 'El archivo es requerido.',
            'file.max' => 'El archivo no debe superar los 5MB.',
            'file.mimes' => 'El archivo debe ser una imagen (jpg, jpeg, png, webp) o PDF.',
            'type.required' => 'El tipo de documento es requerido.',
            'type.in' => 'El tipo de documento no es válido.',
            'side.in' => 'El lado del documento debe ser "front" o "back".',
        ];
    }
}
