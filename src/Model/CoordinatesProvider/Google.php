<?php declare(strict_types=1);

namespace Akozyr\Distance\Model\CoordinatesProvider;

use Akozyr\Distance\Config\Application;
use Akozyr\Distance\Model\CoordinatesProviderInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\InvalidArgumentException;

/**
 * CoordinatesProvider implementation that utilize Google API
 */
class Google implements CoordinatesProviderInterface
{
    private const API_URL = 'https://maps.googleapis.com/maps/api/geocode/json';

    /**
     * @throws GuzzleException
     */
    public function calculate(string $address): array
    {
        $client = new Client();
        $configProvider = new Application();
        $config = $configProvider->getConfig();
        if (empty($config['parameters']['google_maps_api_key'])) {
            throw new \RuntimeException('Missing Google Maps Api Key in Config.');
        }
        $response = $client->request('GET', self::API_URL,
            [
                'query' => [
                    'address' => urldecode($address),
                    'key' => $config['parameters']['google_maps_api_key']
                ]
            ]);

        $result = json_decode($response->getBody()->__toString(), true);
        if (empty($result['status'])
            || $result['status'] !== 'OK'
            || !isset($result['results'][0]['geometry']['location']['lat'])
            || !isset($result['results'][0]['geometry']['location']['lng'])) {
            throw new InvalidArgumentException('No Location Data in the result.');
        }
        return [
            'lat' => $result['results'][0]['geometry']['location']['lat'],
            'lng' => $result['results'][0]['geometry']['location']['lng']
        ];
    }

    public function getTypePrefix(): string
    {
        return "google_maps_";
    }
}