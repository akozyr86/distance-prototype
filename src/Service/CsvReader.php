<?php declare(strict_types=1);

namespace Akozyr\Distance\Service;

class CsvReader
{
    private const CSV_INPUT_PATH = '/../../var/';

    /**
     * @param string $filename
     * @return array
     */
    public function read(string $filename): array
    {
        $path = realpath(__DIR__) . self::CSV_INPUT_PATH . $filename;
        $result = [];
        $csvToRead = fopen($path, 'r');
        while (($data = fgetcsv($csvToRead, null, '-')) !== false) {
            $result[] = $data;
        }
        fclose($csvToRead);
        return $result;
    }
}