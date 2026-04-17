<?php

declare(strict_types=1);

namespace App\SharedKernel\Presentation\Mappers;

use App\CustomerBC\Application\Exceptions\CustomerAlreadyExistsException;
use App\CustomerBC\Application\Exceptions\CustomerNotFoundException;
use App\UserBC\Application\Exceptions\InvalidCredentialsException;
use App\UserBC\Application\Exceptions\UserAlreadyExistsException;
use App\UserBC\Application\Exceptions\UserDisabledException;
use App\UserBC\Application\Exceptions\UserNotFoundException;
use App\SharedKernel\Application\Exception\ApplicationException;
use App\SharedKernel\Domain\Exception\DomainException;
use App\SharedKernel\Domain\Exception\InvalidAddressException;
use App\SharedKernel\Domain\Exception\InvalidDateException;
use App\SharedKernel\Domain\Exception\InvalidDniException;
use App\SharedKernel\Domain\Exception\InvalidEmailException;
use App\SharedKernel\Domain\Exception\InvalidMoneyException;
use App\SharedKernel\Domain\Exception\InvalidNameException;
use App\SharedKernel\Domain\Exception\InvalidPhoneException;
use App\SharedKernel\Domain\Exception\InvalidUuidException;
use App\SharedKernel\Infrastructure\Exception\DatabaseException;
use App\SharedKernel\Infrastructure\Exception\InfrastructureException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Throwable;

final class ErrorMapper
{
    public static function map(Throwable $e): JsonResponse
    {
        $status = self::resolveStatusCode($e);
        
        self::logError($e, $status);

        if ($e instanceof ValidationException) {
            return new JsonResponse([
                'error' => 'Datos inválidos.',
                'details' => $e->errors(),
            ], 422);
        }

        $message = ($status >= 500) 
            ? 'Ha ocurrido un error interno en el servidor.' 
            : $e->getMessage();

        return new JsonResponse([
            'error'   => $message,
        ], $status);
    }

    private static function resolveStatusCode(Throwable $e): int
    {
        $map = self::getMap();

        if (isset($map[get_class($e)])) {
            return $map[get_class($e)];
        }

        foreach ($map as $class => $code) {
            if ($e instanceof $class) {
                return $code;
            }
        }

        return 500;
    }

    private static function logError(Throwable $e, int $status): void
    {
        $context = [
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString(),
        ];

        if ($status >= 500) {
            Log::critical("SERVER_ERROR: " . $e->getMessage(), $context);
        } else {
            Log::notice("CLIENT_ERROR ($status): " . $e->getMessage());
        }
    }

    private static function getMap(): array
    {
        return [
            // Domain
            InvalidAddressException::class => 422,
            InvalidDateException::class => 422,
            InvalidDniException::class => 422,
            InvalidEmailException::class => 422,
            InvalidMoneyException::class => 422,
            InvalidNameException::class => 422,
            InvalidPhoneException::class => 422,
            InvalidUuidException::class => 422,
            DomainException::class => 400,

            // Application
            CustomerNotFoundException::class => 404,
            CustomerAlreadyExistsException::class => 409,
            UserNotFoundException::class => 404,
            UserAlreadyExistsException::class => 409,
            InvalidCredentialsException::class => 401,
            UserDisabledException::class => 403,
            ApplicationException::class => 409,

            // Infrastructure
            DatabaseException::class => 500,
            InfrastructureException::class => 503,
            
            // Laravel Native
            ValidationException::class => 422,
        ];
    }
}
