<?php

declare(strict_types=1);

namespace App\SharedKernel\Domain\ValueObjects;

use App\SharedKernel\Domain\Exceptions\InvalidPhoneException;

final class PhoneVO
{
    private const DEFAULT_COUNTRY_CODE = '+57';

    private function __construct(
        private readonly string $countryCode,
        private readonly string $number
    ) {}

    public static function create(string $number, string $countryCode = self::DEFAULT_COUNTRY_CODE): self
    {
        $number = preg_replace('/[^0-9]/', '', $number);
        $countryCode = trim($countryCode);

        self::validate($countryCode, $number);

        return new self($countryCode, $number);
    }

    private static function validate(string $countryCode, string $number): void
    {
        if ($number === '') {
            throw new InvalidPhoneException('empty');
        }

        if (!preg_match('/^\+\d{1,4}$/', $countryCode)) {
            throw new InvalidPhoneException('invalid_country_code');
        }

        if (strlen($number) < 7) {
            throw new InvalidPhoneException('too_short');
        }

        if (strlen($number) > 15) {
            throw new InvalidPhoneException('too_long');
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

    public function getNationalNumber(): string
    {
        return $this->number;
    }

    public function getInternationalFormat(): string
    {
        return "{$this->countryCode}{$this->number}";
    }

    public function isColombian(): bool
    {
        return $this->countryCode === self::DEFAULT_COUNTRY_CODE;
    }

    public function equals(self $other): bool
    {
        return $this->countryCode === $other->countryCode && $this->number === $other->number;
    }

    public function __toString(): string
    {
        return $this->getInternationalFormat();
    }
}
