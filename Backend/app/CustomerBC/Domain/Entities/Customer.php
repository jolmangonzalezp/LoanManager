<?php

declare(strict_types=1);

namespace App\CustomerBC\Domain\Entities;

use App\CustomerBC\Domain\ValueObjects\CustomerIdVO;
use App\SharedKernel\Domain\ValueObjects\DateVO;
use App\SharedKernel\Domain\ValueObjects\PersonVO;

final class Customer
{
    private function __construct(
        private readonly CustomerIdVO $id,
        private readonly PersonVO $personalData,
        private readonly DateVO $createdAt,
        private readonly bool $enabled
    ) {}

    public static function create(
        PersonVO $personalData,
        ?DateVO $createdAt = null
    ): self {
        $id = CustomerIdVO::generate();
        $createdAt = $createdAt ?? DateVO::now();

        return new self($id, $personalData, $createdAt, true);
    }

    public static function reconstitute(
        CustomerIdVO $id,
        PersonVO $personalData,
        DateVO $createdAt,
        bool $enabled
    ): self {
        return new self($id, $personalData, $createdAt, $enabled);
    }

    public function getId(): CustomerIdVO
    {
        return $this->id;
    }

    public function getPersonalData(): PersonVO
    {
        return $this->personalData;
    }

    public function getCreatedAt(): DateVO
    {
        return $this->createdAt;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function disable(): self
    {
        return new self($this->id, $this->personalData, $this->createdAt, false);
    }

    public function enable(): self
    {
        return new self($this->id, $this->personalData, $this->createdAt, true);
    }
}
