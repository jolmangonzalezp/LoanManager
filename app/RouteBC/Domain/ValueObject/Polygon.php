<?php

declare(strict_types=1);

namespace App\RouteBC\Domain\ValueObject;

final class Polygon
{
    private function __construct(
        private readonly array $coordinates
    ) {}

    public static function fromArray(array $coordinates): self
    {
        if (empty($coordinates)) {
            throw new \InvalidArgumentException('Polygon must have at least 3 coordinate pairs');
        }

        return new self($coordinates);
    }

    public static function fromGeoJSON(string $json): self
    {
        $data = json_decode($json, true);
        if (! $data || ! isset($data['coordinates'][0])) {
            throw new \InvalidArgumentException('Invalid GeoJSON polygon');
        }

        return new self($data['coordinates'][0]);
    }

    public function toArray(): array
    {
        return $this->coordinates;
    }

    public function toGeoJSON(): array
    {
        return [
            'type' => 'Polygon',
            'coordinates' => [$this->coordinates],
        ];
    }

    public function toJson(): string
    {
        return json_encode($this->coordinates);
    }
}
