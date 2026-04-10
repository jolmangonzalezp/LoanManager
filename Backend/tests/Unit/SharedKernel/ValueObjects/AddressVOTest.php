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

    it('compares equality correctly', function () {
        $address1 = AddressVO::create('Calle 123 # 45-67');
        $address2 = AddressVO::create('Calle 123 # 45-67');
        $address3 = AddressVO::create('Calle 456 # 78-90');

        expect($address1->equals($address2))->toBeTrue();
        expect($address1->equals($address3))->toBeFalse();
    });

    it('is case-insensitive for equality', function () {
        $address1 = AddressVO::create('CALLE 123 BOGOTA');
        $address2 = AddressVO::create('calle 123 bogota');

        expect($address1->equals($address2))->toBeTrue();
    });

    it('casts to string', function () {
        $address = AddressVO::create('Calle 123 # 45-67');

        expect((string) $address)->toBe('Calle 123 # 45-67');
    });

    it('messages are user-friendly', function () {
        $e = new InvalidAddressException('empty');
        expect($e->getMessage())->toBe('La dirección es requerida');

        $e = new InvalidAddressException('too_short');
        expect($e->getMessage())->toBe('La dirección es muy corta');
    });
});
