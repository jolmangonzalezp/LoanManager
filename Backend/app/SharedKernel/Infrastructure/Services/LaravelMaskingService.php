<?php

declare(strict_types=1);

namespace App\SharedKernel\Infrastructure\Services;

use App\SharedKernel\Domain\Ports\MaskingService;

final class LaravelMaskingService implements MaskingService
{
    public function maskEmail(string $email): string
    {
        $atPosition = strpos($email, '@');

        if ($atPosition === false) {
            return $this->mask($email, 4);
        }

        $localPart = substr($email, 0, $atPosition);
        $domain = substr($email, $atPosition);

        $maskedLocal = $this->mask($localPart, 1);

        return $maskedLocal.$domain;
    }

    public function maskPhone(string $phone): string
    {
        return $this->maskEnd($phone, 4);
    }

    public function maskDni(string $dni, string $type): string
    {
        return $this->maskEnd($dni, 4);
    }

    public function mask(string $value, int $visibleAtStart = 4, string $maskChar = '*'): string
    {
        $length = strlen($value);

        if ($length <= 4) {
            $visible = (int) ceil($length / 2);
            $maskedLength = $length - $visible;

            return str_repeat($maskChar, $maskedLength).substr($value, -$visible);
        }

        $maskedLength = $length - $visibleAtStart;

        return str_repeat($maskChar, $maskedLength).substr($value, -$visibleAtStart);
    }

    public function maskEnd(string $value, int $visibleAtEnd = 4, string $maskChar = '*'): string
    {
        $length = strlen($value);

        if ($length <= 4) {
            $visible = (int) ceil($length / 2);
            $maskedLength = $length - $visible;

            return str_repeat($maskChar, $maskedLength).substr($value, -$visible);
        }

        $maskedLength = $length - $visibleAtEnd;

        return str_repeat($maskChar, $maskedLength).substr($value, -$visibleAtEnd);
    }
}
