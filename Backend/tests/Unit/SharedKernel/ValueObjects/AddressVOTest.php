<?php

use App\SharedKernel\Domain\ValueObjects\AddressVO;
use App\SharedKernel\Domain\Exceptions\InvalidAddressException;

describe('AddressVO', function () {
    it('creates with valid address', function () {
        $address = AddressVO::create('Calle 123 # 45-67, Bogotá');

        expect($address->getValue())->toBe('Calle 123 # 45-67, Bogotá');
    });

    it('trims whitespace', function () {
        $address = AddressVO::create('  Calle 123 # 45-67  ');

        expect($address->getValue())->toBe('Calle 123 # 45-67');
    });

    it('throws on empty address', function () {
        expect(fn () => AddressVO::create(''))->toThrow(InvalidAddressException::class);
    });

    it('throws on address too short', function () {
        expect(fn () => AddressVO::create('Calle 1'))->toThrow(InvalidAddressException::class);
    });

    it('messages are user-friendly', function () {
        $e = new InvalidAddressException('La dirección es requerida');
        expect($e->getMessage())->toBe('La dirección es requerida');
    });
});