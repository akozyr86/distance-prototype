<?php declare(strict_types=1);

namespace Akozyr\Distance\Model\CoordinatesProvider;

use Akozyr\Distance\Config\Application;
use Akozyr\Distance\Model\CoordinatesProviderInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\InvalidArgumentException;

/**
 * CoordinatesProvider implementation that utilize PositionStack API
 */
class PositionStack implements CoordinatesProviderInterface
{
    private const API_URL = 'http://api.positionstack.com/v1/forward';

    /**
     * @throws GuzzleException
     */
    public function calculate(string $address): array
    {
        $client = new Client();
        $configProvider = new Application();
        $config = $configProvider->getConfig();
        if (empty($config['parameters']['position_stack_api_key'])) {
            throw new \RuntimeException('Missing PositionStack Api Key in Config.');
        }
        $response = $client->request('GET', self::API_URL,
            [
                'query' => [
                    'query' => $address,
                    'access_key' => $config['parameters']['position_stack_api_key']
                ]
            ]);

        $result = json_decode($response->getBody()->__toString(), true);
        if (!isset($result['data'][0]['latitude'])
            || !isset($result['data'][0]['longitude'])) {
            throw new InvalidArgumentException('No Location Data in the result.');
        }
        return [
            'lat' => $result['data'][0]['latitude'],
            'lng' => $result['data'][0]['longitude']
        ];
    }


    public function getTypePrefix(): string
    {
        return "position_stack_";
    }
}