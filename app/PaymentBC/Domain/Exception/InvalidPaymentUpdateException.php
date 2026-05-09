<?php

declare(strict_types=1);

namespace App\PaymentBC\Domain\Exception;

use App\SharedKernel\Domain\Exception\DomainException;

final class InvalidPaymentUpdateException extends DomainException {}
