<?php

declare(strict_types=1);

namespace App\SharedKernel\Domain\ValueObjects;

final class PersonVO implements \Stringable
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

    public function getFullName(): string
    {
        return $this->name->getFullName();
    }

    public function getShortName(): string
    {
        return $this->name->getShortName();
    }

    public function withName(NameVO $name): self
    {
        return new self($name, $this->dni, $this->phone, $this->address, $this->email);
    }

    public function withDni(DniVO $dni): self
    {
        return new self($this->name, $dni, $this->phone, $this->address, $this->email);
    }

    public function withPhone(PhoneVO $phone): self
    {
        return new self($this->name, $this->dni, $phone, $this->address, $this->email);
    }

    public function withAddress(AddressVO $address): self
    {
        return new self($this->name, $this->dni, $this->phone, $address, $this->email);
    }

    public function withEmail(?EmailVO $email): self
    {
        return new self($this->name, $this->dni, $this->phone, $this->address, $email);
    }

    public function equals(self $other): bool
    {
        return $this->name->equals($other->name)
            && $this->dni->equals($other->dni)
            && $this->phone->equals($other->phone)
            && $this->address->equals($other->address)
            && ($this->email?->equals($other->email) ?? $other->email === null);
    }

    public function __toString(): string
    {
        return $this->getFullName();
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name->getFullName(),
            'dni' => $this->dni->getFormatted(),
            'email' => $this->email?->getValue(),
            'phone' => $this->phone->getInternationalFormat(),
            'address' => $this->address->getValue(),
        ];
    }
}
