<?php

use App\SharedKernel\Domain\Exceptions\InvalidNameException;
use App\SharedKernel\Domain\ValueObjects\NameVO;

describe('NameVO', function () {
    it('creates with first, last and second last name', function () {
        $name = NameVO::create('Juan', 'García', 'López');

        expect($name->getFirstName())->toBe('Juan');
        expect($name->getLastName())->toBe('García');
        expect($name->getSecondLastName())->toBe('López');
        expect($name->getFullName())->toBe('Juan García López');
        expect($name->getShortName())->toBe('Juan García');
    });

    it('creates with all name parts', function () {
        $name = NameVO::create('Juan', 'García', 'López', 'Pablo');

        expect($name->getFullName())->toBe('Juan Pablo García López');
        expect($name->getMiddleName())->toBe('Pablo');
        expect($name->getSecondLastName())->toBe('López');
    });

    it('sanitizes input', function () {
        $name = NameVO::create('  Juan   Pablo ', 'García', 'López');

        expect($name->getFullName())->toBe('Juan Pablo García López');
    });

    it('throws on empty first name', function () {
        expect(fn () => NameVO::create('', 'García', 'López'))->toThrow(InvalidNameException::class);
    });

    it('throws on empty last name', function () {
        expect(fn () => NameVO::create('Juan', '', 'López'))->toThrow(InvalidNameException::class);
    });

    it('throws on empty second last name', function () {
        expect(fn () => NameVO::create('Juan', 'García', ''))->toThrow(InvalidNameException::class);
    });

    it('throws on empty middle name', function () {
        expect(fn () => NameVO::create('Juan', 'García', 'López', ''))->toThrow(InvalidNameException::class);
    });

    it('throws on invalid characters', function () {
        expect(fn () => NameVO::create('Juan123', 'García', 'López'))->toThrow(InvalidNameException::class);
    });

    it('throws on names too short', function () {
        expect(fn () => NameVO::create('J', 'García', 'López'))->toThrow(InvalidNameException::class);
    });

    it('ignores valid middle name', function () {
        $name = NameVO::create('Juan', 'García', 'López', 'Pablo');

        expect($name->getMiddleName())->toBe('Pablo');
    });

    it('ignores null middle name', function () {
        $name = NameVO::create('Juan', 'García', 'López');

        expect($name->getMiddleName())->toBeNull();
    });

    it('compares equality correctly', function () {
        $name1 = NameVO::create('Juan', 'García', 'López', 'Pablo');
        $name2 = NameVO::create('Juan', 'García', 'López', 'Pablo');
        $name3 = NameVO::create('Pedro', 'García', 'López');

        expect($name1->equals($name2))->toBeTrue();
        expect($name1->equals($name3))->toBeFalse();
    });

    it('casts to string', function () {
        $name = NameVO::create('Juan', 'García', 'López');

        expect((string) $name)->toBe('Juan García López');
    });

    it('messages are user-friendly', function () {
        $e = new InvalidNameException('empty');
        expect($e->getMessage())->toBe('El nombre es requerido');

        $e = new InvalidNameException('too_short');
        expect($e->getMessage())->toBe('El nombre debe tener al menos 2 caracteres');

        $e = new InvalidNameException('invalid_chars');
        expect($e->getMessage())->toBe('El nombre contiene caracteres inválidos');
    });
});
