<?php

declare(strict_types=1);

namespace App\CustomerBC\Presenter\Controllers;

use App\CustomerBC\Application\Request\UploadCustomerDocumentRequest;
use App\CustomerBC\Application\Service\CustomerDocumentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final readonly class CustomerDocumentController
{
    public function __construct(
        private CustomerDocumentService $documentService,
    ) {}

    public function index(Request $request, string $customerId): JsonResponse
    {
        $documents = $this->documentService->getByCustomerId($customerId);
        return response()->json($documents);
    }

    public function store(UploadCustomerDocumentRequest $request, string $customerId): JsonResponse
    {
        $user = $request->user();
        if (!$user?->roles?->pluck('slug')?->contains('admin')) {
            return response()->json(['error' => 'No tienes permisos para subir documentos.'], 403);
        }

        $response = $this->documentService->upload(
            $request->file('file'),
            $request->input('type'),
            $customerId,
            $user->getAuthIdentifier(),
            $request->input('side'),
        );

        return response()->json($response->toArray(), 201);
    }

    public function destroy(Request $request, string $customerId, string $documentId): JsonResponse
    {
        $user = $request->user();
        if (!$user?->roles?->pluck('slug')?->contains('admin')) {
            return response()->json(['error' => 'No tienes permisos para eliminar documentos.'], 403);
        }

        $success = $this->documentService->delete($documentId);

        if (!$success) {
            return response()->json(['error' => 'Documento no encontrado.'], 404);
        }

        return response()->json(['message' => 'Documento eliminado exitosamente.']);
    }
}
