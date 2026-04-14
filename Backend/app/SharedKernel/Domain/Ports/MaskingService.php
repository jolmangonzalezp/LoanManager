<?php

declare(strict_types=1);

namespace App\SharedKernel\Domain\Ports;

interface MaskingService
{

    public function mask(string $value): string;

    public function maskEnd(string $value): string;
}
