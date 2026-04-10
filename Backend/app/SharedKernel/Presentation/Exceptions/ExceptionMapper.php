<?php

declare(strict_types=1);

namespace App\SharedKernel\Presentation\Exceptions;

use App\SharedKernel\Application\Exceptions\ApplicationException;
use App\SharedKernel\Application\Exceptions\ConflictException;
use App\SharedKernel\Application\Exceptions\ForbiddenException;
use App\SharedKernel\Application\Exceptions\NotFoundException as AppNotFoundException;
use App\SharedKernel\Application\Exceptions\ServiceUnavailableException;
use App\SharedKernel\Application\Exceptions\UnauthorizedException;
use App\SharedKernel\Application\Exceptions\ValidationException;
use App\SharedKernel\Domain\Exceptions\AggregateNotFoundException;
use App\SharedKernel\Domain\Exceptions\DomainException;
use App\SharedKernel\Domain\Exceptions\EntityNotFoundException;
use App\SharedKernel\Infrastructure\Exceptions\InfrastructureException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

final class ExceptionMapper
{
    public function map(Throwable $e, Request $request): JsonResponse
    {
        $response = match (true) {
            $e instanceof DomainException => $this->mapDomainException($e),
            $e instanceof ApplicationException => $this->mapApplicationException($e),
            $e instanceof InfrastructureException => $this->mapInfrastructureException($e),
            default => $this->mapUnknownException($e),
        };

        $traceId = $request->attributes->get('trace_id');

        if ($traceId) {
            $response['trace_id'] = $traceId;
        }

        return response()->json($response, $this->getHttpStatus($e));
    }

    private function mapDomainException(DomainException $e): array
    {
        return [
            'error' => [
                'type' => 'DOMAIN_ERROR',
                'code' => $this->extractCode($e),
                'message' => $e->getMessage(),
            ],
        ];
    }

    private function mapApplicationException(ApplicationException $e): array
    {
        return [
            'error' => [
                'type' => 'APPLICATION_ERROR',
                'code' => $this->extractCode($e),
                'message' => $e->getMessage(),
            ],
        ];
    }

    private function mapInfrastructureException(InfrastructureException $e): array
    {
        return [
            'error' => [
                'type' => 'INFRASTRUCTURE_ERROR',
                'code' => $this->extractCode($e),
                'message' => $e->getMessage(),
            ],
        ];
    }

    private function mapUnknownException(Throwable $e): array
    {
        return [
            'error' => [
                'type' => 'INTERNAL_ERROR',
                'code' => 'INTERNAL_ERROR',
                'message' => 'Error interno del servidor',
            ],
        ];
    }

    private function extractCode(Throwable $e): string
    {
        $className = (new \ReflectionClass($e))->getShortName();

        return preg_replace('/Exception$/', '', $className) ?? $className;
    }

    private function getHttpStatus(Throwable $e): int
    {
        return match (true) {
            $e instanceof AppNotFoundException,
            $e instanceof EntityNotFoundException,
            $e instanceof AggregateNotFoundException => 404,
            $e instanceof ValidationException,
            $e instanceof DomainException => 422,
            $e instanceof UnauthorizedException => 401,
            $e instanceof ForbiddenException => 403,
            $e instanceof ConflictException => 409,
            $e instanceof ServiceUnavailableException,
            $e instanceof InfrastructureException => 503,
            default => 500,
        };
    }
}
