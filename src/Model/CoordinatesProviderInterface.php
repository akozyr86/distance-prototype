<?php declare(strict_types=1);

namespace Akozyr\Distance\Model;

/**
 * Interface accepts to address and get coordinates as array ['lat'=>8.14123, 'lng'=>-71.12412];
 */
interface CoordinatesProviderInterface
{
    /**
     * calculate coordinates
     * @param string $address
     * @return array
     */
    public function calculate(string $address): array;

    /**
     * method returns unique code for the Map Provider to use in the result building
     * @return string
     */
    public function getTypePrefix(): string;

}