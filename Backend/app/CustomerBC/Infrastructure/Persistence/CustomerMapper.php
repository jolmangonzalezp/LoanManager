<?php

declare(strict_types=1);

namespace App\CustomerBC\Infrastructure\Persistence;

use App\CustomerBC\Domain\Entities\Customer;
use App\CustomerBC\Domain\ValueObjects\CustomerIdVO;
use App\CustomerBC\Infrastructure\Models\CustomerModel;
use App\SharedKernel\Domain\Ports\EncryptionService;
use App\SharedKernel\Domain\ValueObjects\AddressVO;
use App\SharedKernel\Domain\ValueObjects\DateVO;
use App\SharedKernel\Domain\ValueObjects\DniType;
use App\SharedKernel\Domain\ValueObjects\DniVO;
use App\SharedKernel\Domain\ValueObjects\EmailVO;
use App\SharedKernel\Domain\ValueObjects\NameVO;
use App\SharedKernel\Domain\ValueObjects\PersonVO;
use App\SharedKernel\Domain\ValueObjects\PhoneVO;

final class CustomerMapper
{
    public function __construct(
        private readonly EncryptionService $encryption
    ) {}

    public function toDomain(CustomerModel $model): Customer
    {
        $personalData = $this->toPersonVO($model);

        $id = CustomerIdVO::fromString($model->id);
        $createdAt = DateVO::create($model->created_at);
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
            'first_name' => $this->encryption->encrypt($name->getFirstName()),
            'last_name' => $this->encryption->encrypt($name->getLastName()),
            'second_last_name' => $this->encryption->encrypt($name->getSecondLastName()),
            'middle_name' => $name->getMiddleName()
                ? $this->encryption->encrypt($name->getMiddleName())
                : null,
            'dni_number' => $this->encryption->encrypt($dni->getNumber()),
            'dni_hash' => $this->encryption->hash($dni->getNumber()),
            'dni_type' => $dni->getType()->value,
            'phone_number' => $this->encryption->encrypt($phone->getNumber()),
            'phone_country_code' => $phone->getCountryCode(),
            'address' => $this->encryption->encrypt($person->getAddress()->getValue()),
            'email' => $person->getEmail()?->getValue(),
            'enabled' => $customer->isEnabled(),
            'created_at' => $customer->getCreatedAt()->getFormatted('Y-m-d H:i:s'),
        ];
    }

    private function toPersonVO(CustomerModel $model): PersonVO
    {
        $name = NameVO::create(
            $this->encryption->decrypt($model->first_name),
            $this->encryption->decrypt($model->last_name),
            $this->encryption->decrypt($model->second_last_name),
            $model->middle_name
                ? $this->encryption->decrypt($model->middle_name)
                : null
        );

        $dni = DniVO::create(
            $this->encryption->decrypt($model->dni_number),
            DniType::from($model->dni_type)
        );

        $phone = PhoneVO::create(
            $this->encryption->decrypt($model->phone_number),
            $model->phone_country_code ?? '57'
        );

        $address = AddressVO::create(
            $this->encryption->decrypt($model->address)
        );

        $email = $model->email
            ? EmailVO::create($model->email)
            : null;

        return PersonVO::create($name, $dni, $phone, $address, $email);
    }
}
