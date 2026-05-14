<?php

declare(strict_types=1);

namespace App\RouteBC\Domain\Service;

use App\RouteBC\Domain\Aggregate\Zone;
use App\RouteBC\Domain\Repository\ZoneRepositoryInterface;

final class GeoLocationService
{
    public function __construct(
        private readonly ZoneRepositoryInterface $zoneRepo
    ) {}

    public function pointInWhichZone(float $lat, float $lng): ?Zone
    {
        $zones = $this->zoneRepo->findAll();
        $pt = ['type' => 'Point', 'coordinates' => [$lng, $lat]];

        foreach ($zones as $zone) {
            $poly = $zone->getPolygon()->toGeoJSON();
            if ($this->pointInPolygon($pt, $poly)) {
                return $zone;
            }
        }

        return null;
    }

    private function pointInPolygon(array $point, array $polygon): bool
    {
        $coords = $polygon['coordinates'][0] ?? [];
        $x = $point['coordinates'][0];
        $y = $point['coordinates'][1];
        $inside = false;

        $n = count($coords);
        $j = $n - 1;

        for ($i = 0; $i < $n; $i++) {
            $xi = $coords[$i][0];
            $yi = $coords[$i][1];
            $xj = $coords[$j][0];
            $yj = $coords[$j][1];

            if (($yi > $y) !== ($yj > $y) &&
                $x < ($xj - $xi) * ($y - $yi) / ($yj - $yi) + $xi
            ) {
                $inside = ! $inside;
            }
            $j = $i;
        }

        return $inside;
    }

    public function reverseGeocode(float $lat, float $lng): string
    {
        $token = config('services.mapbox.token', env('MAPBOX_TOKEN'));

        if (! $token) {
            return "{$lat}, {$lng}";
        }

        try {
            $url = "https://api.mapbox.com/geocoding/v5/mapbox.places/{$lng},{$lat}.json?access_token={$token}&language=es";

            $response = file_get_contents($url);
            $data = json_decode($response, true);

            if (isset($data['features'][0]['place_name_es'])) {
                return $data['features'][0]['place_name_es'];
            }

            if (isset($data['features'][0]['place_name'])) {
                return $data['features'][0]['place_name'];
            }

            return "{$lat}, {$lng}";
        } catch (\Exception) {
            return "{$lat}, {$lng}";
        }
    }
}
