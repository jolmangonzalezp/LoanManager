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
use Illuminate\Http\Request;

final class UpdateCustomerRequest
{
    public static function fromArray(string $id, Request $request): UpdateCustomerCommand
    {
        $id = CustomerIdVO::fromString($id);

        $person = PersonVO::create(
            NameVO::create(
                $request['name']['first_name'] ?? '',
                    $request['name']['last_name'] ?? '',
                    $request['name']['second_last_name'] ?? '',
                    $request['name']['middle_name'] ?? null
            ),
            DniVO::create(
                $request['dni']['number'] ?? '',
                DniType::from($request['dni']['type'] ?? 'CC')
            ),
            PhoneVO::create($request['phone'] ?? ''),
            AddressVO::create($request['address'] ?? ''),
            ! empty($request['email']) ? EmailVO::create($request['email']) : null
        );

        return new UpdateCustomerCommand($id, $person);
    }
}
