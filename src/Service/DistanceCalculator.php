<?php declare(strict_types=1);

namespace Akozyr\Distance\Service;

/**
 * Class provides calculation of the distance using ‘haversine’ formula
 */
class DistanceCalculator
{
    /**
     * @param array $from
     * @param array $to
     * @return float
     */
    public function calculate(array $from, array $to): float
    {
        $latFrom = deg2rad($from['lat']);
        $lonFrom = deg2rad($from['lng']);
        $latTo = deg2rad($to['lat']);
        $lonTo = deg2rad($to['lng']);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
                cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
        return $angle * 6371000;
    }
}