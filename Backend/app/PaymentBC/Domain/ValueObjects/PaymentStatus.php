<?php

declare(strict_types=1);

namespace App\PaymentBC\Domain\ValueObjects;

enum PaymentStatus: string
{
    case PENDING = 'pending';
    case VALIDATED = 'validated';
    case APPLIED = 'applied';
    case REJECTED = 'rejected';
    case REFUNDED = 'refunded';
}
