<?php

use App\SharedKernel\Domain\Exceptions\InvalidMoneyException;
use App\SharedKernel\Domain\ValueObjects\Currency;
use App\SharedKernel\Domain\ValueObjects\MoneyVO;

describe('MoneyVO', function () {
    it('creates with positive amount', function () {
        $money = MoneyVO::create(100000);

        expect($money->getAmount())->toBe(100000);
        expect($money->getCurrency())->toBe(Currency::COP);
    });

    it('creates with custom currency', function () {
        $money = MoneyVO::create(100, Currency::USD);

        expect($money->getCurrency())->toBe(Currency::USD);
    });

    it('creates zero amount', function () {
        $money = MoneyVO::zero();

        expect($money->getAmount())->toBe(0);
        expect($money->isZero())->toBeTrue();
    });

    it('formats as Colombian peso', function () {
        $money = MoneyVO::create(1500000);

        expect($money->getFormatted())->toBe('$1.500.000');
    });

    it('formats USD correctly', function () {
        $money = MoneyVO::create(1500, Currency::USD);

        expect($money->getFormatted())->toBe('US$1,500');
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

    it('multiplies amount', function () {
        $money = MoneyVO::create(50000);

        $result = $money->multiply(3);

        expect($result->getAmount())->toBe(150000);
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

    it('equals considers currency', function () {
        $money1 = MoneyVO::create(100000, Currency::COP);
        $money2 = MoneyVO::create(100000, Currency::USD);

        expect($money1->equals($money2))->toBeFalse();
    });

    it('casts to string', function () {
        $money = MoneyVO::create(100000);

        expect((string) $money)->toBe('$100.000');
    });

    it('messages are user-friendly', function () {
        $e = new InvalidMoneyException('negative');
        expect($e->getMessage())->toBe('El monto no puede ser negativo');
    });
});
