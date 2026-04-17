<?php

declare(strict_types=1);

namespace App\CustomerBC\Presentation\Requests;

use App\CustomerBC\Application\DTO\CreateCustomerRequest;
use App\SharedKernel\Domain\ValueObject\DniType;
use Illuminate\Foundation\Http\FormRequest;

final class CreateCustomerRequestMapper extends FormRequest
{
    public function rules(): array
    {
        return [
            'first_name' => 'required|string|min:2',
            'last_name' => 'required|string|min:2',
            'second_last_name' => 'required|string|min:2',
            'middle_name' => 'nullable|string|min:2',
            'dni_number' => 'required|string',
            'dni_type' => 'required|string|in:CC,CE,NIT,PASSPORT',
            'phone_number' => 'required|string',
            'phone_country_code' => 'required|string',
            'address' => 'required|string|min:10',
            'email' => 'nullable|email',
        ];
    }

    public function toDTO(): CreateCustomerRequest
    {
        return new CreateCustomerRequest(
            $this->input('first_name'),
            $this->input('last_name'),
            $this->input('second_last_name'),
            $this->input('middle_name'),
            $this->input('dni_number'),
            DniType::from($this->input('dni_type')),
            $this->input('phone_number'),
            $this->input('phone_country_code'),
            $this->input('address'),
            $this->input('email')
        );
    }
}
