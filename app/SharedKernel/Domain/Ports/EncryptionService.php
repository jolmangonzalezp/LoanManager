<?php

declare(strict_types=1);

namespace App\SharedKernel\Domain\Ports;

interface EncryptionService
{
    public function encrypt(string $plaintext): string;

    public function decrypt(string $ciphertext): string;

    public function hash(string $value): string;

    public function verify(string $value, string $hash): bool;
}
