<?php

namespace App\CustomerBC\Infrastructure\Request;

use App\CustomerBC\Application\CQRS\Command\UpdateCustomerCommand;
use App\CustomerBC\Domain\ValueObject\CustomerIdVO;
use App\SharedKernel\Domain\ValueObject\AddressVO;
use App\SharedKernel\Domain\ValueObject\DniType;
use App\SharedKernel\Domain\ValueObject\DniVO;
use App\SharedKernel\Domain\ValueObject\EmailVO;
use App\SharedKernel\Domain\ValueObject\NameVO;
use App\SharedKernel\Domain\ValueObject\PersonVO;
use App\SharedKernel\Domain\ValueObject\PhoneVO;

final class UpdateCustomerRequest
{
    public static function fromArray(array $data): UpdateCustomerCommand
    {
        $id = CustomerIdVO::fromString($data['id']);

        $person = PersonVO::create(
            NameVO::create(
                $data['first_name'] ?? '',
                $data['last_name'] ?? '',
                $data['second_last_name'] ?? '',
                $data['middle_name'] ?? null
            ),
            DniVO::create(
                $data['dni_number'] ?? '',
                DniType::from($data['dni_type'] ?? 'CC')
            ),
            PhoneVO::create($data['phone'] ?? ''),
            AddressVO::create($data['address'] ?? ''),
            ! empty($data['email']) ? EmailVO::create($data['email']) : null
        );

        return new UpdateCustomerCommand($id, $person);
    }
}
