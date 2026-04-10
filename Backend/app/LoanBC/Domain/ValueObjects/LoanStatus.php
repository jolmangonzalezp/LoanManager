<?php

declare(strict_types=1);

namespace App\LoanBC\Domain\ValueObjects;

enum LoanStatus: string
{
    case ACTIVE = 'active';
    case PAID = 'paid';
    case DEFAULTED = 'defaulted';
    case CANCELLED = 'cancelled';
}
