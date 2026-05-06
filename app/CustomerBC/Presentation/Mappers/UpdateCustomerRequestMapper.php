<?php

declare(strict_types=1);

namespace App\CustomerBC\Presentation\Mappers;

use App\CustomerBC\Application\CQRS\Command\UpdateCustomerCommand;
use App\CustomerBC\Domain\ValueObject\CustomerIdVO;
use App\SharedKernel\Domain\ValueObject\AddressVO;
use App\SharedKernel\Domain\ValueObject\DniType;
use App\SharedKernel\Domain\ValueObject\DniVO;
use App\SharedKernel\Domain\ValueObject\EmailVO;
use App\SharedKernel\Domain\ValueObject\NameVO;
use App\SharedKernel\Domain\ValueObject\PersonVO;
use App\SharedKernel\Domain\ValueObject\PhoneVO;

final class UpdateCustomerRequestMapper
{
    private const DEFAULT_COUNTRY_CODE = '+57';

    public function fromRequest(array $data): UpdateCustomerCommand
    {
        $firstName = $data['first_name'] ?? '';
        $middleName = $data['middle_name'] ?? null;
        $lastName = $data['last_name'] ?? '';
        $secondLastName = $data['second_last_name'] ?? '';

        $name = NameVO::create(
            $firstName,
            $lastName,
            $secondLastName,
            $middleName
        );

        $dni = DniVO::create(
            $data['dni_number'] ?? '',
            DniType::from($data['dni_type'] ?? 'CC')
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

        return new UpdateCustomerCommand(CustomerIdVO::fromString($data['id']), $personalData);
    }

    private function formatCountryCode(string $code): string
    {
        $code = trim($code);

        return ! str_starts_with($code, '+') ? '+'.$code : $code;
    }
}
