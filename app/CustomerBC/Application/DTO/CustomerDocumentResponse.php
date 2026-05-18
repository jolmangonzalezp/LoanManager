<?php

declare(strict_types=1);

namespace App\CustomerBC\Application\DTO;

final class CustomerDocumentResponse
{
    public function __construct(
        public readonly string $id,
        public readonly string $type,
        public readonly ?string $side,
        public readonly string $originalName,
        public readonly string $url,
        public readonly string $mimeType,
        public readonly int $size,
        public readonly string $createdAt,
    ) {}

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'side' => $this->side,
            'originalName' => $this->originalName,
            'url' => $this->url,
            'mimeType' => $this->mimeType,
            'size' => $this->size,
            'createdAt' => $this->createdAt,
        ];
    }
}
