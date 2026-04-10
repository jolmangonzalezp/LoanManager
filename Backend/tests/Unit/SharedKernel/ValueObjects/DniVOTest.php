<?php

use App\SharedKernel\Domain\Exceptions\InvalidDniException;
use App\SharedKernel\Domain\ValueObjects\DniType;
use App\SharedKernel\Domain\ValueObjects\DniVO;

describe('DniVO', function () {
    it('creates CC type with valid number', function () {
        $dni = DniVO::create('123456789', DniType::CC);

        expect($dni->getNumber())->toBe('123456789');
        expect($dni->getType())->toBe(DniType::CC);
    });

    it('creates CE type', function () {
        $dni = DniVO::create('123456789012', DniType::CE);

        expect($dni->getType())->toBe(DniType::CE);
    });

    it('creates NIT type', function () {
        $dni = DniVO::create('123456789', DniType::NIT);

        expect($dni->getType())->toBe(DniType::NIT);
    });

    it('removes non-digit characters', function () {
        $dni = DniVO::create('12.345.678', DniType::CC);

        expect($dni->getNumber())->toBe('12345678');
    });

    it('throws on empty number', function () {
        expect(fn () => DniVO::create('', DniType::CC))->toThrow(InvalidDniException::class);
    });

    it('throws on CC with wrong length', function () {
        expect(fn () => DniVO::create('12345', DniType::CC))->toThrow(InvalidDniException::class);
    });

    it('formats CC with dots', function () {
        $dni = DniVO::create('123456789', DniType::CC);

        expect($dni->getFormatted())->toBe('123.456.789');
    });

    it('compares equality correctly', function () {
        $dni1 = DniVO::create('12345678', DniType::CC);
        $dni2 = DniVO::create('12345678', DniType::CC);
        $dni3 = DniVO::create('87654321', DniType::CC);

        expect($dni1->equals($dni2))->toBeTrue();
        expect($dni1->equals($dni3))->toBeFalse();
    });

    it('casts to string', function () {
        $dni = DniVO::create('12345678', DniType::CC);

        expect((string) $dni)->toBe('CC:12345678');
    });

    it('messages are user-friendly', function () {
        $e = new InvalidDniException('empty');
        expect($e->getMessage())->toBe('El número de documento es requerido');

        $e = new InvalidDniException('invalid_length');
        expect($e->getMessage())->toBe('La longitud del documento no es válida para el tipo especificado');

        $e = new InvalidDniException('invalid_format');
        expect($e->getMessage())->toBe('El formato del documento no es válido para el tipo especificado');
    });
});
