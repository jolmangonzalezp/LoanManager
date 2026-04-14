<?php

declare(strict_types=1);

namespace App\SharedKernel\Infrastructure\Services;

use App\SharedKernel\Domain\Ports\MaskingService;

final class LaravelMaskingService implements MaskingService
{
    public function mask(string $value): string
    {
        $length = strlen($value);

        if ($length <= 4) {
            $visible = (int) ceil($length / 2);
            $maskedLength = $length - $visible;

            return str_repeat('*', $maskedLength).substr($value, -$visible);
        }

        $maskedLength = $length - 4;

        return str_repeat('*', $maskedLength).substr($value, -4);
    }

    public function maskEnd(string $value): string
    {
        $length = strlen($value);

        if ($length <= 4) {
            $visible = (int) ceil($length / 2);
            $maskedLength = $length - $visible;

            return str_repeat('*', $maskedLength).substr($value, -$visible);
        }

        $maskedLength = $length - 4;

        return str_repeat('*', $maskedLength).substr($value, -4);
    }
}
