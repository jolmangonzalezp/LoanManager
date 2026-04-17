<?php

declare(strict_types=1);

namespace App\SharedKernel\Infrastructure\Services;

use App\SharedKernel\Domain\Ports\MaskingService;

final class LaravelMaskingService implements MaskingService
{
    public function mask(string $value): string
    {
        $length = mb_strlen($value, 'UTF-8');

        if ($length <= 4) {
            $visible = (int) ceil($length / 2);
            $maskedLength = $length - $visible;

            return str_repeat('*', $maskedLength).mb_substr($value, -$visible, null, 'UTF-8');
        }

        $maskedLength = $length - 4;

        return str_repeat('*', $maskedLength).mb_substr($value, -4, null, 'UTF-8');
    }

    public function maskEnd(string $value): string
    {
        $length = mb_strlen($value, 'UTF-8');

        if ($length <= 4) {
            $visible = (int) ceil($length / 2);
            $maskedLength = $length - $visible;

            return str_repeat('*', $maskedLength).mb_substr($value, -$visible, null, 'UTF-8');
        }

        $maskedLength = $length - 4;

        return str_repeat('*', $maskedLength).mb_substr($value, -4, null, 'UTF-8');
    }
}
