<?php

declare(strict_types=1);

namespace App\SharedKernel\Domain\ValueObjects;

use App\SharedKernel\Domain\Exceptions\InvalidEmailException;

final class EmailVO implements \Stringable
{
    private const LOCAL_MAX_LENGTH = 64;

    private const DOMAIN_MAX_LENGTH = 255;

    private const LOCAL_PATTERN = '/^[a-zA-Z0-9.!#$%&\'*+\/=?^_`{|}~-]+$/';

    private const DOMAIN_PATTERN = '/^[a-zA-Z0-9]([a-zA-Z0-9-]*[a-zA-Z0-9])?(\.[a-zA-Z0-9]([a-zA-Z0-9-]*[a-zA-Z0-9])?)*$/';

    private function __construct(
        private readonly string $value
    ) {}

    public static function create(string $email): self
    {
        $email = strtolower(trim($email));

        self::validate($email);

        return new self($email);
    }

    private static function validate(string $email): void
    {
        $atPosition = strpos($email, '@');

        if ($atPosition === false || $atPosition === 0) {
            throw new InvalidEmailException('missing_at');
        }

        $localPart = substr($email, 0, $atPosition);
        $domainPart = substr($email, $atPosition + 1);

        self::validateLocalPart($localPart);
        self::validateDomainPart($domainPart);
    }

    private static function validateLocalPart(string $local): void
    {
        $validators = [
            fn () => $local !== '' ?: throw new InvalidEmailException('empty_local'),
            fn () => strlen($local) <= self::LOCAL_MAX_LENGTH ?: throw new InvalidEmailException('local_too_long'),
            fn () => preg_match(self::LOCAL_PATTERN, $local) === 1 ?: throw new InvalidEmailException('invalid_local_chars'),
        ];

        foreach ($validators as $validator) {
            $validator();
        }
    }

    private static function validateDomainPart(string $domain): void
    {
        $validators = [
            fn () => $domain !== '' ?: throw new InvalidEmailException('empty_domain'),
            fn () => strlen($domain) <= self::DOMAIN_MAX_LENGTH ?: throw new InvalidEmailException('domain_too_long'),
            fn () => preg_match(self::DOMAIN_PATTERN, $domain) === 1 ?: throw new InvalidEmailException('invalid_domain_format'),
        ];

        foreach ($validators as $validator) {
            $validator();
        }
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getLocalPart(): string
    {
        return explode('@', $this->value)[0];
    }

    public function getDomain(): string
    {
        return explode('@', $this->value)[1];
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
