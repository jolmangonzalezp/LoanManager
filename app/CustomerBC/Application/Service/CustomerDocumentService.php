<?php

declare(strict_types=1);

namespace App\CustomerBC\Application\Service;

use App\CustomerBC\Application\DTO\CustomerDocumentResponse;
use App\CustomerBC\Domain\Repository\CustomerDocumentRepositoryInterface;
use App\CustomerBC\Domain\ValueObject\CustomerDocumentType;
use App\CustomerBC\Infrastructure\Persistence\Model\CustomerDocument;
use App\CustomerBC\Infrastructure\Persistence\Model\CustomerModel;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Ramsey\Uuid\Uuid;

final class CustomerDocumentService
{
    public function __construct(
        private readonly CustomerDocumentRepositoryInterface $documentRepo,
    ) {}

    public function upload(UploadedFile $file, string $type, string $customerId, string $userId, ?string $side = null): CustomerDocumentResponse
    {
        $id = Uuid::uuid4()->toString();
        $extension = $file->getClientOriginalExtension();
        $filename = $id . '.' . $extension;
        $path = 'customers/' . $customerId . '/' . $type . '/' . $filename;

        Storage::disk('public')->put($path, file_get_contents($file->getRealPath()));

        $document = new CustomerDocument();
        $document->id = $id;
        $document->customer_id = $customerId;
        $document->type = $type;
        $document->side = $side;
        $document->filename = $filename;
        $document->original_name = $file->getClientOriginalName();
        $document->mime_type = $file->getMimeType();
        $document->size = $file->getSize();
        $document->uploaded_by = $userId;
        $this->documentRepo->save($document);

        $this->updateDocumentsComplete($customerId);

        return $this->toResponse($document);
    }

    public function delete(string $documentId): bool
    {
        $document = $this->documentRepo->findById($documentId);
        if (!$document) {
            return false;
        }

        Storage::disk('public')->delete('customers/' . $document->customer_id . '/' . $document->type . '/' . $document->filename);
        $this->documentRepo->delete($document);

        $this->updateDocumentsComplete($document->customer_id);

        return true;
    }

    /** @return CustomerDocumentResponse[] */
    public function getByCustomerId(string $customerId): array
    {
        $documents = $this->documentRepo->findByCustomerId($customerId);
        return array_map(fn (CustomerDocument $doc) => $this->toResponse($doc), $documents);
    }

    private function updateDocumentsComplete(string $customerId): void
    {
        $personPhoto = $this->documentRepo->findByCustomerIdAndType($customerId, 'person_photo');
        $identityFront = $this->documentRepo->findByCustomerIdAndTypeAndSide($customerId, 'identity_document', 'front');
        $identityBack = $this->documentRepo->findByCustomerIdAndTypeAndSide($customerId, 'identity_document', 'back');
        $housePhoto = $this->documentRepo->findByCustomerIdAndType($customerId, 'house_photo');

        $isComplete = $personPhoto && $identityFront && $identityBack && $housePhoto;

        CustomerModel::where('id', $customerId)->update(['documents_complete' => $isComplete]);
    }

    private function toResponse(CustomerDocument $document): CustomerDocumentResponse
    {
        return new CustomerDocumentResponse(
            id: $document->id,
            type: $document->type,
            side: $document->side,
            originalName: $document->original_name,
            url: Storage::disk('public')->url('customers/' . $document->customer_id . '/' . $document->type . '/' . $document->filename),
            mimeType: $document->mime_type,
            size: $document->size,
            createdAt: $document->created_at?->toIso8601String() ?? '',
        );
    }
}
