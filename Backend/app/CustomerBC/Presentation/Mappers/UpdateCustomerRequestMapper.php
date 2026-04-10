<?php

declare(strict_types=1);

namespace App\CustomerBC\Presentation\Mappers;

use App\CustomerBC\Application\Commands\UpdateCustomerCommand;
use App\SharedKernel\Domain\ValueObjects\AddressVO;
use App\SharedKernel\Domain\ValueObjects\DniType;
use App\SharedKernel\Domain\ValueObjects\DniVO;
use App\SharedKernel\Domain\ValueObjects\EmailVO;
use App\SharedKernel\Domain\ValueObjects\NameVO;
use App\SharedKernel\Domain\ValueObjects\PersonVO;
use App\SharedKernel\Domain\ValueObjects\PhoneVO;

final class UpdateCustomerRequestMapper
{
    public function fromRequest(array $data): UpdateCustomerCommand
    {
        $name = NameVO::create(
            $data['first_name'],
            $data['last_name'],
            $data['second_last_name'],
            $data['middle_name'] ?? null
        );

        $dni = DniVO::create(
            $data['dni_number'],
            DniType::from($data['dni_type'])
        );

        $phone = PhoneVO::create(
            $data['phone_number'],
            $data['phone_country_code']
        );

        $address = AddressVO::create($data['address']);

        $email = isset($data['email'])
            ? EmailVO::create($data['email'])
            : null;

        $personalData = PersonVO::create($name, $dni, $phone, $address, $email);

        return new UpdateCustomerCommand($data['id'], $personalData);
    }
}
