<?php

declare(strict_types=1);

namespace App\SharedKernel\Domain\ValueObjects;

final class PersonVO
{
    public function __construct(
        private readonly NameVO $name,
        private readonly DniVO $dni,
        private readonly PhoneVO $phone,
        private readonly AddressVO $address,
        private readonly ?EmailVO $email = null
    ) {}

    public static function create(
        NameVO $name,
        DniVO $dni,
        PhoneVO $phone,
        AddressVO $address,
        ?EmailVO $email = null
    ): self {
        return new self($name, $dni, $phone, $address, $email);
    }

    public function getName(): NameVO
    {
        return $this->name;
    }

    public function getDni(): DniVO
    {
        return $this->dni;
    }

    public function getPhone(): PhoneVO
    {
        return $this->phone;
    }

    public function getAddress(): AddressVO
    {
        return $this->address;
    }

    public function getEmail(): ?EmailVO
    {
        return $this->email;
    }
    public function equals(self $other): bool
    {
        return $this->name->equals($other->name)
            && $this->dni->equals($other->dni)
            && $this->phone->equals($other->phone)
            && $this->address->equals($other->address)
            && ($this->email?->equals($other->email) ?? $other->email === null);
    }
}
