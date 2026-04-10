<?php

use App\SharedKernel\Domain\Exceptions\InvalidEmailException;
use App\SharedKernel\Domain\ValueObjects\EmailVO;

describe('EmailVO', function () {
    it('creates with valid email', function () {
        $email = EmailVO::create('juan@example.com');

        expect($email->getValue())->toBe('juan@example.com');
        expect($email->getLocalPart())->toBe('juan');
        expect($email->getDomain())->toBe('example.com');
    });

    it('lowercases email', function () {
        $email = EmailVO::create('JUAN@EXAMPLE.COM');

        expect($email->getValue())->toBe('juan@example.com');
    });

    it('trims whitespace', function () {
        $email = EmailVO::create('  juan@example.com  ');

        expect($email->getValue())->toBe('juan@example.com');
    });

    it('throws on empty email', function () {
        expect(fn () => EmailVO::create(''))->toThrow(InvalidEmailException::class);
    });

    it('throws when missing @', function () {
        expect(fn () => EmailVO::create('juanexample.com'))->toThrow(InvalidEmailException::class);
    });

    it('throws on empty local part', function () {
        expect(fn () => EmailVO::create('@example.com'))->toThrow(InvalidEmailException::class);
    });

    it('throws on empty domain', function () {
        expect(fn () => EmailVO::create('juan@'))->toThrow(InvalidEmailException::class);
    });

    it('throws on invalid local part characters', function () {
        expect(fn () => EmailVO::create('ju an@example.com'))->toThrow(InvalidEmailException::class);
    });

    it('throws on invalid domain format', function () {
        expect(fn () => EmailVO::create('juan@.com'))->toThrow(InvalidEmailException::class);
    });

    it('creates email with subdomain', function () {
        $email = EmailVO::create('juan@mail.example.com');

        expect($email->getDomain())->toBe('mail.example.com');
    });

    it('compares equality correctly', function () {
        $email1 = EmailVO::create('juan@example.com');
        $email2 = EmailVO::create('juan@example.com');
        $email3 = EmailVO::create('pedro@example.com');

        expect($email1->equals($email2))->toBeTrue();
        expect($email1->equals($email3))->toBeFalse();
    });

    it('casts to string', function () {
        $email = EmailVO::create('juan@example.com');

        expect((string) $email)->toBe('juan@example.com');
    });

    it('messages are user-friendly', function () {
        $e = new InvalidEmailException('empty');
        expect($e->getMessage())->toBe('El correo electrónico es requerido');

        $e = new InvalidEmailException('missing_at');
        expect($e->getMessage())->toBe('El correo electrónico debe contener @');

        $e = new InvalidEmailException('empty_local');
        expect($e->getMessage())->toBe('La parte local del correo es requerida');

        $e = new InvalidEmailException('empty_domain');
        expect($e->getMessage())->toBe('El dominio del correo es requerido');

        $e = new InvalidEmailException('invalid_local_chars');
        expect($e->getMessage())->toBe('La parte local del correo contiene caracteres inválidos');

        $e = new InvalidEmailException('invalid_domain_format');
        expect($e->getMessage())->toBe('El formato del dominio no es válido');
    });
});
