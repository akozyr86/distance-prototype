<?php declare(strict_types=1);

namespace Akozyr\Distance\Config;

use Symfony\Component\Yaml\Yaml;

/**
 * Service class to fetch configuration for the tool.
 */
class Application
{
    private const PATH_TO_FILE = '/../../config/params.yml';

    /**
     * @return array
     */
    public function getConfig(): array
    {
        return Yaml::parseFile(realpath(__DIR__) . self::PATH_TO_FILE);
    }
}