<?php

use App\SharedKernel\Domain\Exceptions\InvalidMoneyException;
use App\SharedKernel\Domain\ValueObjects\MoneyVO;

describe('MoneyVO', function () {
    it('creates with positive amount', function () {
        $money = MoneyVO::create(100000);

        expect($money->getAmount())->toBe(100000);
    });

    it('creates zero amount', function () {
        $money = MoneyVO::zero();

        expect($money->getAmount())->toBe(0);
        expect($money->isZero())->toBeTrue();
    });

    it('adds amounts', function () {
        $money1 = MoneyVO::create(100000);
        $money2 = MoneyVO::create(50000);

        $result = $money1->add($money2);

        expect($result->getAmount())->toBe(150000);
    });

    it('subtracts amounts', function () {
        $money1 = MoneyVO::create(100000);
        $money2 = MoneyVO::create(30000);

        $result = $money1->subtract($money2);

        expect($result->getAmount())->toBe(70000);
    });

    it('returns zero on negative result', function () {
        $money1 = MoneyVO::create(30000);
        $money2 = MoneyVO::create(100000);

        $result = $money1->subtract($money2);

        expect($result->getAmount())->toBe(0);
    });

    it('throws on negative amount', function () {
        expect(fn () => MoneyVO::create(-1000))->toThrow(InvalidMoneyException::class);
    });

    it('checks greater than', function () {
        $money1 = MoneyVO::create(100000);
        $money2 = MoneyVO::create(50000);

        expect($money1->isGreaterThan($money2))->toBeTrue();
        expect($money2->isGreaterThan($money1))->toBeFalse();
    });

    it('checks less than', function () {
        $money1 = MoneyVO::create(50000);
        $money2 = MoneyVO::create(100000);

        expect($money1->isLessThan($money2))->toBeTrue();
        expect($money2->isLessThan($money1))->toBeFalse();
    });

    it('compares equality correctly', function () {
        $money1 = MoneyVO::create(100000);
        $money2 = MoneyVO::create(100000);
        $money3 = MoneyVO::create(50000);

        expect($money1->equals($money2))->toBeTrue();
        expect($money1->equals($money3))->toBeFalse();
    });

    it('messages are user-friendly', function () {
        $e = new InvalidMoneyException('Monto invalido');
        expect($e->getMessage())->toBe('Monto invalido');
    });
});