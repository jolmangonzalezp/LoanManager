<?php

declare(strict_types=1);

namespace App\CustomerBC\Infrastructure\Persistence\Repository;

use App\CustomerBC\Domain\Repository\CustomerDocumentRepositoryInterface;
use App\CustomerBC\Infrastructure\Persistence\Model\CustomerDocument;

final class EloquentCustomerDocumentRepository implements CustomerDocumentRepositoryInterface
{
    public function findByCustomerId(string $customerId): array
    {
        return CustomerDocument::where('customer_id', $customerId)
            ->orderBy('created_at', 'desc')
            ->get()
            ->all();
    }

    public function findById(string $id): ?CustomerDocument
    {
        return CustomerDocument::find($id);
    }

    public function findByCustomerIdAndType(string $customerId, string $type): ?CustomerDocument
    {
        return CustomerDocument::where('customer_id', $customerId)
            ->where('type', $type)
            ->first();
    }

    public function findByCustomerIdAndTypeAndSide(string $customerId, string $type, string $side): ?CustomerDocument
    {
        return CustomerDocument::where('customer_id', $customerId)
            ->where('type', $type)
            ->where('side', $side)
            ->first();
    }

    public function save(CustomerDocument $document): void
    {
        $document->save();
    }

    public function delete(CustomerDocument $document): void
    {
        $document->delete();
    }

    public function countByCustomerId(string $customerId): int
    {
        return CustomerDocument::where('customer_id', $customerId)->count();
    }
}
