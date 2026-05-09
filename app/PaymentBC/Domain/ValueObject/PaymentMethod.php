<?php

declare(strict_types=1);

namespace App\PaymentBC\Domain\ValueObject;

enum PaymentMethod: string
{
    case CASH = 'cash';
    case BANK_TRANSFER = 'bank_transfer';
    case CARD = 'card';
    case CHECK = 'check';
    case OTHER = 'other';
}
