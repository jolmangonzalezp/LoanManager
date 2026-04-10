<?php

declare(strict_types=1);

namespace App\SharedKernel\Domain\Ports;

interface MaskingService
{
    public function maskEmail(string $email): string;

    public function maskPhone(string $phone): string;

    public function maskDni(string $dni, string $type): string;

    public function mask(string $value, int $visibleAtStart = 4, string $maskChar = '*'): string;

    public function maskEnd(string $value, int $visibleAtEnd = 4, string $maskChar = '*'): string;
}
