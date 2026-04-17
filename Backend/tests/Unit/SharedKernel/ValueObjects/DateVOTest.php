<?php

use App\SharedKernel\Domain\ValueObjects\DateVO;
use App\SharedKernel\Domain\Exceptions\InvalidDateException;

describe('DateVO', function () {
    it('creates from DateTimeInterface', function () {
        $dateTime = new DateTimeImmutable('2024-01-15');
        $date = DateVO::fromDateTime($dateTime);

        expect($date->getValue())->toEqual($dateTime);
        expect($date->getFormatted())->toBe('2024-01-15');
    });

    it('creates from string', function () {
        $date = DateVO::fromString('2024-01-15');

        expect($date->getFormatted())->toBe('2024-01-15');
    });

    it('creates now', function () {
        $date = DateVO::now();

        expect($date->getFormatted())->toBe(date('Y-m-d'));
    });

    it('compares isAfter correctly', function () {
        $date1 = DateVO::fromString('2024-01-15');
        $date2 = DateVO::fromString('2024-01-16');

        expect($date2->isAfter($date1))->toBeTrue();
        expect($date1->isAfter($date2))->toBeFalse();
    });

    it('compares isBefore correctly', function () {
        $date1 = DateVO::fromString('2024-01-15');
        $date2 = DateVO::fromString('2024-01-16');

        expect($date1->isBefore($date2))->toBeTrue();
        expect($date2->isBefore($date1))->toBeFalse();
    });

    it('compares isSameDay correctly', function () {
        $date1 = DateVO::fromString('2024-01-15 10:00:00');
        $date2 = DateVO::fromString('2024-01-15 22:00:00');
        $date3 = DateVO::fromString('2024-01-16');

        expect($date1->isSameDay($date2))->toBeTrue();
        expect($date1->isSameDay($date3))->toBeFalse();
    });

    it('compares equality correctly', function () {
        $date1 = DateVO::fromString('2024-01-15');
        $date2 = DateVO::fromString('2024-01-15');
        $date3 = DateVO::fromString('2024-01-16');

        expect($date1->equals($date2))->toBeTrue();
        expect($date1->equals($date3))->toBeFalse();
    });

    it('casts to string', function () {
        $date = DateVO::fromString('2024-01-15');

        expect((string) $date)->toBe('2024-01-15');
    });

    it('throws on empty date', function () {
        expect(fn () => DateVO::fromString(''))->toThrow(InvalidDateException::class);
    });
});