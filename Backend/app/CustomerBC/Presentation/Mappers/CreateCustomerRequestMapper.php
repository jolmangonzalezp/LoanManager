<?php

declare(strict_types=1);

namespace App\CustomerBC\Presentation\Mappers;

use App\CustomerBC\Application\Commands\CreateCustomerCommand;
use App\SharedKernel\Domain\ValueObjects\AddressVO;
use App\SharedKernel\Domain\ValueObjects\DniType;
use App\SharedKernel\Domain\ValueObjects\DniVO;
use App\SharedKernel\Domain\ValueObjects\EmailVO;
use App\SharedKernel\Domain\ValueObjects\NameVO;
use App\SharedKernel\Domain\ValueObjects\PersonVO;
use App\SharedKernel\Domain\ValueObjects\PhoneVO;

final class CreateCustomerRequestMapper
{
    private const DEFAULT_COUNTRY_CODE = '+57';

    public function fromRequest(array $data): CreateCustomerCommand
    {
        $nameData = $data['name'] ?? [];
        
        $firstName = $nameData['first_name'] ?? $data['first_name'] ?? '';
        $middleName = $nameData['last_name'] ?? $data['last_name'] ?? null;
        $lastName = $nameData['second_last_name'] ?? $data['second_last_name'] ?? '';
        $secondLastName = $nameData['third_last_name'] ?? $data['third_last_name'] ?? '';
        
        $name = NameVO::create(
            $firstName,
            $lastName,
            $secondLastName,
            $middleName ?: null
        );

        $dniData = $data['dni'] ?? [];
        $dni = DniVO::create(
            $dniData['number'] ?? $data['dni_number'] ?? '',
            DniType::from($dniData['type'] ?? $data['dni_type'] ?? 'CC')
        );

        $phone = PhoneVO::create(
            $data['phone'] ?? $data['phone_number'] ?? '',
            $this->formatCountryCode($data['phone_country_code'] ?? '57')
        );

        $addressData = $data['address'] ?? null;
        $addressString = '';
        if ($addressData) {
            $street = $addressData['street'] ?? '';
            $city = $addressData['city'] ?? '';
            $country = $addressData['country'] ?? 'CO';
            if ($street || $city) {
                $addressString = trim($street . ($city ? ', ' . $city : '') . ($country ? ', ' . $country : ''));
            }
        }
        $address = $addressString 
            ? AddressVO::create($addressString) 
            : AddressVO::create('Colombia - Sin dirección específica');

        $email = isset($data['email'])
            ? EmailVO::create($data['email'])
            : null;

        $personalData = PersonVO::create($name, $dni, $phone, $address, $email);

        return new CreateCustomerCommand($personalData);
    }

    private function formatCountryCode(string $code): string
    {
        $code = trim($code);

        return ! str_starts_with($code, '+') ? '+'.$code : $code;
    }
}
