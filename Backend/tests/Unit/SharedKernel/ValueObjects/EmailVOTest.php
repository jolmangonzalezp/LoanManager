<?php

use App\SharedKernel\Domain\Exceptions\InvalidEmailException;
use App\SharedKernel\Domain\ValueObjects\EmailVO;

describe('EmailVO', function () {
    it('creates with valid email', function () {
        $email = EmailVO::create('juan@example.com');

        expect($email->getValue())->toBe('juan@example.com');
    });

    it('lowercases email', function () {
        $email = EmailVO::create('JUAN@Example.COM');

        expect($email->getValue())->toBe('juan@example.com');
    });

    it('throws on empty email', function () {
        expect(fn () => EmailVO::create(''))->toThrow(InvalidEmailException::class);
    });

    it('throws on invalid email', function () {
        expect(fn () => EmailVO::create('invalid-email'))->toThrow(InvalidEmailException::class);
    });

    it('creates email with subdomain', function () {
        $email = EmailVO::create('juan@mail.example.com');

        expect($email->getValue())->toBe('juan@mail.example.com');
    });

    it('compares equality correctly', function () {
        $email1 = EmailVO::create('juan@example.com');
        $email2 = EmailVO::create('juan@example.com');
        $email3 = EmailVO::create('juan2@example.com');

        expect($email1->equals($email2))->toBeTrue();
        expect($email1->equals($email3))->toBeFalse();
    });

    it('messages are user-friendly', function () {
        $e = new InvalidEmailException('El email es requerido.');
        expect($e->getMessage())->toBe('El email es requerido.');
    });
});