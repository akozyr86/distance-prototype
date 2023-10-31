<?php declare(strict_types=1);

namespace Akozyr\Distance\Model;

use \RuntimeException;


class CoordinatesProviderFactory
{
    public const SUPPORTED_ENGINES = [
        "google" => \Akozyr\Distance\Model\CoordinatesProvider\Google::class,
        "pstack" => \Akozyr\Distance\Model\CoordinatesProvider\PositionStack::class
    ];

    public function create(string $type): CoordinatesProviderInterface
    {
        if (!isset(self::SUPPORTED_ENGINES[$type])) {
            throw new RuntimeException(sprintf("Unexpected Map provider %s", $type));
        }
        $className = self::SUPPORTED_ENGINES[$type];
        return new $className();
    }
}