<?php

declare(strict_types=1);

namespace App\PaymentBC\Domain\DTO;

use App\SharedKernel\Domain\ValueObject\MoneyVO;

final readonly class PaymentResult
{
    public function __construct(
        public MoneyVO $interestPortion,
        public MoneyVO $capitalPortion,
        public MoneyVO $remainingDebt
    ) {}
}
