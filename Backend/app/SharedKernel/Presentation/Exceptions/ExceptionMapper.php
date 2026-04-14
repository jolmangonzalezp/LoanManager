<?php

declare(strict_types=1);

namespace App\SharedKernel\Presentation\Exceptions;

use App\SharedKernel\Application\Exceptions\ApplicationException;
use App\SharedKernel\Application\Exceptions\ConflictException;
use App\SharedKernel\Application\Exceptions\ForbiddenException;
use App\SharedKernel\Application\Exceptions\NotFoundException;
use App\SharedKernel\Application\Exceptions\ServiceUnavailableException;
use App\SharedKernel\Application\Exceptions\UnauthorizedException;
use App\SharedKernel\Application\Exceptions\ValidationException;
use App\SharedKernel\Domain\Exceptions\DomainException;
use App\SharedKernel\Infrastructure\Exceptions\InfrastructureException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

final class ExceptionMapper
{
    private const HTTP_MAP = [
        DomainException::class => 422,
        NotFoundException::class => 404,
        ValidationException::class => 422,
        UnauthorizedException::class => 401,
        ForbiddenException::class => 403,
        ConflictException::class => 409,
        ServiceUnavailableException::class => 503,
        ApplicationException::class => 500,
        InfrastructureException::class => 503,
    ];

    private const TYPE_MAP = [
        DomainException::class => 'DOMAIN_ERROR',
        ApplicationException::class => 'APPLICATION_ERROR',
        InfrastructureException::class => 'INFRASTRUCTURE_ERROR',
    ];

    public function map(Throwable $e, Request $request): JsonResponse
    {
        $response = $this->buildResponse($e);

        $traceId = $request->attributes->get('trace_id');
        if ($traceId) {
            $response['trace_id'] = $traceId;
        }

        $httpCode = $this->resolveHttpCode($e);

        return response()->json($response, $httpCode);
    }

    private function resolveHttpCode(Throwable $e): int
    {
        foreach (self::HTTP_MAP as $exceptionClass => $code) {
            if ($e instanceof $exceptionClass) {
                return $code;
            }
        }
        return 500;
    }

    private function resolveType(Throwable $e): string
    {
        foreach (self::TYPE_MAP as $exceptionClass => $type) {
            if ($e instanceof $exceptionClass) {
                return $type;
            }
        }
        return 'INTERNAL_ERROR';
    }

    private function buildResponse(Throwable $e): array
    {
        $code = (new \ReflectionClass($e))->getShortName();

        return [
            'error' => [
                'type' => $this->resolveType($e),
                'code' => $code,
                'message' => $e->getMessage(),
            ],
        ];
    }
}