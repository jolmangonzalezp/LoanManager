<?php

use App\SharedKernel\Domain\Exceptions\InvalidUuidException;
use App\SharedKernel\Domain\ValueObjects\UuidVO;

describe('UuidVO', function () {
    it('generates a valid UUID v7', function () {
        $uuid = UuidVO::generate();

        expect($uuid->getValue())->toMatch('/^[0-9a-f]{8}-[0-9a-f]{4}-7[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i');
    });

    it('creates from valid string', function () {
        $uuid = UuidVO::fromString('550e8400-e29b-41d4-a716-446655440000');

        expect($uuid->getValue())->toBe('550e8400-e29b-41d4-a716-446655440000');
    });

    it('throws on invalid UUID format', function () {
        expect(fn () => UuidVO::fromString('invalid-uuid'))->toThrow(InvalidUuidException::class);
    });

    it('compares equality correctly', function () {
        $uuid1 = UuidVO::fromString('550e8400-e29b-41d4-a716-446655440000');
        $uuid2 = UuidVO::fromString('550e8400-e29b-41d4-a716-446655440000');
        $uuid3 = UuidVO::fromString('660e8400-e29b-41d4-a716-446655440000');

        expect($uuid1->equals($uuid2))->toBeTrue();
        expect($uuid1->equals($uuid3))->toBeFalse();
    });

    it('casts to string', function () {
        $uuid = UuidVO::fromString('550e8400-e29b-41d4-a716-446655440000');

        expect((string) $uuid)->toBe('550e8400-e29b-41d4-a716-446655440000');
    });
});
