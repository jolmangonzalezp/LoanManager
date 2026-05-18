<?php

declare(strict_types=1);

namespace App\CustomerBC\Domain\Repository;

use App\CustomerBC\Infrastructure\Persistence\Model\CustomerDocument;

interface CustomerDocumentRepositoryInterface
{
    /** @return CustomerDocument[] */
    public function findByCustomerId(string $customerId): array;

    public function findById(string $id): ?CustomerDocument;

    public function findByCustomerIdAndType(string $customerId, string $type): ?CustomerDocument;

    public function findByCustomerIdAndTypeAndSide(string $customerId, string $type, string $side): ?CustomerDocument;

    public function save(CustomerDocument $document): void;

    public function delete(CustomerDocument $document): void;

    public function countByCustomerId(string $customerId): int;
}
