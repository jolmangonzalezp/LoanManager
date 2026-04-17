<?php

declare(strict_types=1);

namespace App\LoanBC\Domain\Repository;

interface CustomerNameProvider
{
    /** @return array<string, string> Mapa [id => nombre_completo] */
    public function getNamesMap(array $customerIds): array;
}
