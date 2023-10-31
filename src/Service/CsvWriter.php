<?php declare(strict_types=1);

namespace Akozyr\Distance\Service;

class CsvWriter
{
    private const CSV_INPUT_PATH = '/../../var/';

    /**
     * @param array $data
     * @param string $filename
     * @return void
     */
    public function write(array $data, string $filename): void
    {
        $path = realpath(__DIR__) . self::CSV_INPUT_PATH . $filename . '.csv';
        $fp = fopen($path, 'w');
        foreach ($data as $fields) {
            fputcsv($fp, $fields);
        }
        fclose($fp);
    }
}