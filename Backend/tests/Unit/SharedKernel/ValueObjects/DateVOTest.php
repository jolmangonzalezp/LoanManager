<?php

use App\SharedKernel\Domain\ValueObjects\DateVO;

describe('DateVO', function () {
    it('creates from DateTimeInterface', function () {
        $dateTime = new DateTimeImmutable('2024-01-15');
        $date = DateVO::create($dateTime);

        expect($date->getValue())->toEqual($dateTime);
        expect($date->getFormatted())->toBe('2024-01-15');
    });

    it('creates from string', function () {
        $date = DateVO::create('2024-01-15');

        expect($date->getFormatted())->toBe('2024-01-15');
    });

    it('creates today', function () {
        $date = DateVO::today();

        expect($date->getFormatted())->toBe((new DateTimeImmutable('today'))->format('Y-m-d'));
    });

    it('creates now', function () {
        $date = DateVO::now();

        expect($date->getFormatted('Y-m-d H:i:s'))->toMatch('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/');
        expect($date->getYear())->toBe((int) date('Y'));
    });

    it('extracts date parts', function () {
        $date = DateVO::create('2024-03-15');

        expect($date->getYear())->toBe(2024);
        expect($date->getMonth())->toBe(3);
        expect($date->getDay())->toBe(15);
    });

    it('formats with custom pattern', function () {
        $date = DateVO::create('2024-03-15');

        expect($date->getFormatted('d/m/Y'))->toBe('15/03/2024');
    });

    it('compares isAfter correctly', function () {
        $date1 = DateVO::create('2024-01-15');
        $date2 = DateVO::create('2024-01-16');

        expect($date2->isAfter($date1))->toBeTrue();
        expect($date1->isAfter($date2))->toBeFalse();
    });

    it('compares isBefore correctly', function () {
        $date1 = DateVO::create('2024-01-15');
        $date2 = DateVO::create('2024-01-16');

        expect($date1->isBefore($date2))->toBeTrue();
        expect($date2->isBefore($date1))->toBeFalse();
    });

    it('compares isSameDay correctly', function () {
        $date1 = DateVO::create('2024-01-15 10:00:00');
        $date2 = DateVO::create('2024-01-15 22:00:00');
        $date3 = DateVO::create('2024-01-16');

        expect($date1->isSameDay($date2))->toBeTrue();
        expect($date1->isSameDay($date3))->toBeFalse();
    });

    it('calculates diff in days', function () {
        $date1 = DateVO::create('2024-01-15');
        $date2 = DateVO::create('2024-01-20');

        expect($date1->diffInDays($date2))->toBe(-5);
        expect($date2->diffInDays($date1))->toBe(5);
    });

    it('compares equality correctly', function () {
        $date1 = DateVO::create('2024-01-15');
        $date2 = DateVO::create('2024-01-15');
        $date3 = DateVO::create('2024-01-16');

        expect($date1->equals($date2))->toBeTrue();
        expect($date1->equals($date3))->toBeFalse();
    });

    it('casts to string', function () {
        $date = DateVO::create('2024-01-15');

        expect((string) $date)->toBe('2024-01-15');
    });
});
