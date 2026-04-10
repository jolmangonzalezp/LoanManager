<?php

use App\SharedKernel\Domain\ValueObjects\PhoneVO;
use App\SharedKernel\Domain\Exceptions\InvalidPhoneException;

describe('PhoneVO', function () {
    it('creates with Colombian number by default', function () {
        $phone = PhoneVO::create('3001234567');

        expect($phone->getCountryCode())->toBe('+57');
        expect($phone->getNumber())->toBe('3001234567');
        expect($phone->isColombian())->toBeTrue();
    });

    it('creates with custom country code', function () {
        $phone = PhoneVO::create('3001234567', '+1');

        expect($phone->getCountryCode())->toBe('+1');
        expect($phone->isColombian())->toBeFalse();
    });

    it('removes non-digit characters', function () {
        $phone = PhoneVO::create('(300) 123-4567');

        expect($phone->getNumber())->toBe('3001234567');
    });

    it('formats as international', function () {
        $phone = PhoneVO::create('3001234567');

        expect($phone->getInternationalFormat())->toBe('+573001234567');
    });

    it('throws on empty number', function () {
        expect(fn () => PhoneVO::create(''))->toThrow(InvalidPhoneException::class);
    });

    it('throws on invalid country code', function () {
        expect(fn () => PhoneVO::create('3001234567', 'invalid'))->toThrow(InvalidPhoneException::class);
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

    it('casts to string', function () {
        $phone = PhoneVO::create('3001234567');

        expect((string) $phone)->toBe('+573001234567');
    });

    it('messages are user-friendly', function () {
        $e = new InvalidPhoneException('empty');
        expect($e->getMessage())->toBe('El número de teléfono es requerido');

        $e = new InvalidPhoneException('invalid_country_code');
        expect($e->getMessage())->toBe('El código de país no es válido');

        $e = new InvalidPhoneException('too_short');
        expect($e->getMessage())->toBe('El número de teléfono debe tener entre 7 y 15 dígitos');
    });
});
