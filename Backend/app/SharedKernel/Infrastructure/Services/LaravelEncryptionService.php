<?php

declare(strict_types=1);

namespace App\SharedKernel\Infrastructure\Services;

use App\SharedKernel\Domain\Ports\EncryptionService;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;

final class LaravelEncryptionService implements EncryptionService
{
    public function encrypt(string $plaintext): string
    {
        return Crypt::encryptString($plaintext);
    }

    public function decrypt(string $ciphertext): string
    {
        return Crypt::decryptString($ciphertext);
    }

    public function hash(string $value): string
    {
        return Hash::make($value);
    }

    public function verify(string $value, string $hash): bool
    {
        return Hash::check($value, $hash);
    }
}
