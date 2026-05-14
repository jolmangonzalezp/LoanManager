<?php

declare(strict_types=1);

namespace App\RouteBC\Presenter\Controllers;

use App\RouteBC\Application\DTO\ZoneResponse;
use App\RouteBC\Application\UseCase\CreateZoneUseCase;
use App\RouteBC\Application\UseCase\DeleteZoneUseCase;
use App\RouteBC\Application\UseCase\UpdateZoneUseCase;
use App\RouteBC\Domain\Repository\ZoneRepositoryInterface;
use App\RouteBC\Domain\ValueObject\ZoneIdVO;
use App\RouteBC\Infrastructure\Request\CreateZoneRequest;
use App\RouteBC\Infrastructure\Request\UpdateZoneRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final readonly class ZoneController
{
    public function __construct(
        private CreateZoneUseCase $createZoneUseCase,
        private UpdateZoneUseCase $updateZoneUseCase,
        private DeleteZoneUseCase $deleteZoneUseCase,
        private ZoneRepositoryInterface $zoneRepo,
    ) {}

    public function index(): JsonResponse
    {
        $zones = $this->zoneRepo->findAll();
        $data = array_map(fn ($z) => ZoneResponse::fromEntity($z)->toArray(), $zones);

        return response()->json($data);
    }

    public function show(string $id): JsonResponse
    {
        $zone = $this->zoneRepo->findById(ZoneIdVO::fromString($id));
        if (! $zone) {
            return response()->json(['error' => 'Zone not found'], 404);
        }

        return response()->json(ZoneResponse::fromEntity($zone)->toArray());
    }

    public function store(Request $request): JsonResponse
    {
        $command = CreateZoneRequest::fromArray($request->all());
        $success = $this->createZoneUseCase->execute($command);

        return response()->json($success ? $this->createZoneUseCase->getResponse() : false, 201);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $command = UpdateZoneRequest::fromArray($id, $request->all());
        $success = $this->updateZoneUseCase->execute($command);

        return response()->json($success ? $this->updateZoneUseCase->getResponse() : false);
    }

    public function destroy(string $id): JsonResponse
    {
        $success = $this->deleteZoneUseCase->execute(ZoneIdVO::fromString($id));

        return response()->json($success);
    }
}
