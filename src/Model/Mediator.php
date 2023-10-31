<?php declare(strict_types=1);

namespace Akozyr\Distance\Model;

use Akozyr\Distance\Config\Application;
use Akozyr\Distance\Model\CoordinatesProviderFactory;
use Akozyr\Distance\Service\CsvWriter;
use Akozyr\Distance\Service\CsvReader;
use Akozyr\Distance\Service\DistanceCalculator;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class responsible for processing all logic.
 * Scenario:
 * 1. get CoordinatorProvider based on mode
 * 2. parse input data from csv file
 * 3. get HeadQuarter Coordinates
 * 4. get Input Addresses Coordinates
 * 5. calculate distance between each Input Address and HeadQuarter
 * 6. write CSV with results
 * 7. echo results into console
 */
class Mediator
{
    private ?OutputInterface $output = null;

    /**
     * @param string $filename
     * @param string $mode
     * @param OutputInterface $output
     * @return void
     */
    public function process(string $filename, string $mode, OutputInterface $output): void
    {
        $this->output = $output;
        try {
            $coordinateProviderFactory = new CoordinatesProviderFactory();
            $coordinateProvider = $coordinateProviderFactory->create($mode);
            $configProvider = new Application();
            $config = $configProvider->getConfig();
            if (!isset($config['parameters']['headquarter_address'])) {
                throw new \RuntimeException('Missing HeadQuarter Address in Config.');
            }
            $headquarterCoordinates = $coordinateProvider->calculate($config['parameters']['headquarter_address']);
            $locationCoordinates = [];
            $inputData = $this->readCsv($filename);
            foreach ($inputData as $address) {
                try {
                    $locationCoordinates[] = [
                        'name' => trim($address[0]),
                        'real_address' => trim($address[1]),
                        'coordinates' => $coordinateProvider->calculate($address[1]),
                        'distance' => 0
                    ];
                } catch (GuzzleException $exception) {
                    $output->writeln("Coordinates Provider Exception happened: ");
                    $this->output->writeln($exception->getMessage());
                    $output->writeln(sprintf("Omitting input address %s: ", $address[0]));
                }
            }
            $precision = $config['parameters']['distance_formatLength'] ?? 2;

            foreach ($locationCoordinates as &$location) {
                $location['distance'] = $this->getDistance($headquarterCoordinates, $location['coordinates'],
                    $precision);
            }
            usort($locationCoordinates, fn($a, $b) => $a['distance'] <=> $b['distance']);

            $result = [];
            $start = 1;
            $result[] = ['SortNumber', 'Distance', 'Name', 'Address'];
            foreach ($locationCoordinates as $item) {
                $result[] = [
                    $start++,
                    number_format($item['distance'], $precision, '.', ''),
                    $item['name'],
                    $item['real_address']
                ];
            }
            $name = $coordinateProvider->getTypePrefix() . 'result_' . time();
            $this->writeCsv($result, $name);
            $this->showInConsole($result);
            $output->writeln("Finishing process.");
        } catch (\Throwable $e) {
            $this->output->writeln("-----------------------------------");
            $output->writeln("Unexpected Error happened : ");
            $output->writeln($e->getMessage());
            $output->writeln("Cannot proceed. Finishing process.");
            $this->output->writeln("-----------------------------------");
        }
    }

    /**
     * @param string $filename
     * @return array
     */
    private function readCsv(string $filename): array
    {
        $this->output->writeln("-----------------------------------");
        $this->output->writeln(sprintf("Opening file %s. ", $filename));
        $this->output->writeln("-----------------------------------");
        $reader = new CsvReader();
        return $reader->read($filename);
    }

    /**
     * @param array $headquarterCoordinates
     * @param array $coordinates
     * @param int $precision
     * @return float
     */
    private function getDistance(array $headquarterCoordinates, array $coordinates, int $precision): float
    {
        $calc = new DistanceCalculator();
        $metersDistance = $calc->calculate($headquarterCoordinates, $coordinates);
        return round($metersDistance / 1000, $precision);
    }

    /**
     * @param array $dataToWrite
     * @param string $name
     * @return void
     */
    private function writeCsv(array $dataToWrite, string $name): void
    {
        $this->output->writeln("-----------------------------------");
        $this->output->writeln(sprintf("Writing to file %s.", $name));
        $this->output->writeln("You can find it in the var folder.");
        $this->output->writeln("-----------------------------------");
        $writer = new CsvWriter();
        $writer->write($dataToWrite, $name);
    }

    /**
     * @param array $result
     * @return void
     */
    private function showInConsole(array $result): void
    {
        $strings = [];
        foreach ($result as $line) {
            $strings[] = implode(',', $line);
        };
        $this->output->writeln("Results:");
        $this->output->writeln("-----------------------------------");
        $this->output->writeln(implode(PHP_EOL, $strings));
        $this->output->writeln("-----------------------------------");
    }
}