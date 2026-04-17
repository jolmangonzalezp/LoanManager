<?php

use App\SharedKernel\Domain\ValueObjects\PhoneVO;
use App\SharedKernel\Domain\Exceptions\InvalidPhoneException;

describe('PhoneVO', function () {
    it('creates with Colombian number by default', function () {
        $phone = PhoneVO::create('3001234567');

        expect($phone->getCountryCode())->toBe('+57');
        expect($phone->getNumber())->toBe('3001234567');
    });

    it('creates with custom country code', function () {
        $phone = PhoneVO::create('3001234567', '+1');

        expect($phone->getCountryCode())->toBe('+1');
    });

    it('removes non-digit characters', function () {
        $phone = PhoneVO::create('(300) 123-4567');

        expect($phone->getNumber())->toBe('3001234567');
    });

    it('throws on empty number', function () {
        expect(fn () => PhoneVO::create(''))->toThrow(InvalidPhoneException::class);
    });

    it('throws on number too short', function () {
        expect(fn () => PhoneVO::create('123'))->toThrow(InvalidPhoneException::class);
    });

    it('throws on number too long', function () {
        expect(fn () => PhoneVO::create('1234567890123456'))->toThrow(InvalidPhoneException::class);
    });

    it('compares equality correctly', function () {
        $phone1 = PhoneVO::create('3001234567');
        $phone2 = PhoneVO::create('3001234567');
        $phone3 = PhoneVO::create('3009876543');

        expect($phone1->equals($phone2))->toBeTrue();
        expect($phone1->equals($phone3))->toBeFalse();
    });

    it('messages are user-friendly', function () {
        $e = new InvalidPhoneException('Numero de teléfono es requerido');
        expect($e->getMessage())->toBe('Numero de teléfono es requerido');
    });
});