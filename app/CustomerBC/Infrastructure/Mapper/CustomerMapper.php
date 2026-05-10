<?php

declare(strict_types=1);

namespace App\CustomerBC\Infrastructure\Mapper;

use App\CustomerBC\Domain\Aggregate\Customer;
use App\CustomerBC\Domain\ValueObject\CustomerIdVO;
use App\CustomerBC\Infrastructure\Persistence\Model\CustomerModel;
use App\SharedKernel\Domain\ValueObject\AddressVO;
use App\SharedKernel\Domain\ValueObject\DateVO;
use App\SharedKernel\Domain\ValueObject\DniType;
use App\SharedKernel\Domain\ValueObject\DniVO;
use App\SharedKernel\Domain\ValueObject\EmailVO;
use App\SharedKernel\Domain\ValueObject\NameVO;
use App\SharedKernel\Domain\ValueObject\PersonVO;
use App\SharedKernel\Domain\ValueObject\PhoneVO;

final class CustomerMapper
{
    public function toDomain(CustomerModel $model): Customer
    {
        $personalData = $this->toPersonVO($model);

        $id = CustomerIdVO::fromString($model->id);
        $createdAt = DateVO::fromDateTime($model->created_at);
        $enabled = $model->enabled;

        return Customer::reconstitute($id, $personalData, $createdAt, $enabled);
    }

    public function toPersistence(Customer $customer): array
    {
        $person = $customer->getPersonalData();
        $name = $person->getName();
        $dni = $person->getDni();
        $phone = $person->getPhone();

        return [
            'id' => $customer->getId()->getValue(),
            'first_name' => $name->getFirstName(),
            'last_name' => $name->getLastName(),
            'second_last_name' => $name->getSecondLastName(),
            'middle_name' => $name->getMiddleName(),
            'dni_number' => $dni->getNumber(),
            'dni_hash' => $dni->getHash(),
            'dni_type' => $dni->getType()->value,
            'phone_number' => $phone->getNumber(),
            'phone_country_code' => $phone->getCountryCode(),
            'address' => $person->getAddress()->getValue(),
            'email' => $person->getEmail()?->getValue(),
            'enabled' => $customer->isEnabled(),
            'created_at' => $customer->getCreatedAt()->getFormatted('Y-m-d H:i:s'),
        ];
    }

    private function toPersonVO(CustomerModel $model): PersonVO
    {
        $name = NameVO::create(
            $model->first_name,
            $model->last_name,
            $model->second_last_name,
            $model->middle_name
        );

        $dni = DniVO::create(
            $model->dni_number,
            DniType::from($model->dni_type)
        );

        $phone = PhoneVO::create(
            $model->phone_number,
            $model->phone_country_code ? '+'.ltrim($model->phone_country_code, '+') : '+57'
        );

        $address = AddressVO::create(
            $model->address
        );

        $email = null;
        if ($model->email) {
            $email = EmailVO::create($model->email);
        }

        return PersonVO::create($name, $dni, $phone, $address, $email);
    }
}
