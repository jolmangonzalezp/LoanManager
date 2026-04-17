<?php

declare(strict_types=1);

namespace App\SharedKernel\Domain\ValueObject;

use App\SharedKernel\Domain\Exception\InvalidPhoneException;

final class PhoneVO
{   
    private readonly string $countryCode;
    private readonly string $number;

    private function __construct(
        string $countryCode,
        string $number
    ) {
        $this->countryCode = $countryCode;
        $this->number = $number;
    }

    public static function create(string $number, string $countryCode = '+57'): self
    {
        $number = preg_replace('/[^0-9]/', '', $number);
        $countryCode = trim($countryCode);

        self::validate($countryCode, $number);

        return new self($countryCode, $number);
    }

    private static function validate(string $countryCode, string $number): void
    {
        if ($number === '') {
            throw new InvalidPhoneException('Numero de teléfono es requerido');
        }

        if (!preg_match('/^\+\d{1,4}$/', $countryCode)) {
            throw new InvalidPhoneException('Codigo de país es inválido');
        }

        if (strlen($number) < 7) {
            throw new InvalidPhoneException('Numero de teléfono es invalido');
        }

        if (strlen($number) > 15) {
            throw new InvalidPhoneException('Numero de teléfono es invalido');
        }
    }

    public function getCountryCode(): string
    {
        return $this->countryCode;
    }

    public function getNumber(): string
    {
        return $this->number;
    }
    public function equals(self $other): bool
    {
        return $this->countryCode === $other->countryCode && $this->number === $other->number;
    }
}
