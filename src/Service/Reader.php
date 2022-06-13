<?php

namespace App\Service;

class Reader
{
    public function csvToArray(string $productCsv): array
    {
        $csvOpen = fopen($productCsv, 'rb');

        $isFirstLine = true;
        $lines = [];
        $headers = [];

        // Loop to create the table
        while (($document = fgetcsv($csvOpen, 1000, ';')) !== false) {
            // Checking table entries
            for ($i = 0; $i <= 6; $i++) {
                if (isset($document[$i])) {
                    continue;
                } else {
                    throw new \Exception('There is an error in the csv file.');
                }
            }

            if ($isFirstLine) {
                $headers = [$document[0], 'Status', $document[3], $document[5], 'Created At', 'Slug'];
                $headers = array_map('ucwords', $headers);

                $isFirstLine = false;

                continue;
            }

            // For the formatting rules
            $date = new \DateTime($document[6]);
            $slug = preg_replace('/[^A-Za-z0-9\-]/', '-', str_replace(' ', '_',
                strtolower($document[1])));
            $price = str_replace('.', ',', $document[3]);
            $currency = strip_tags($document[4]);
            $description = str_replace('<br/>', "\n", str_replace('\r', "\n",
                $document[5]));

            $content = [
                $headers[0] => $document[0],
                'Status' => $document[2] ? 'Enable' : 'Disable',
                $headers[2] => $price . $currency,
                $headers[3] => $description,
                'Created At' => $date->format('l, j-M-Y H:i:s T'),
                'Slug' => $slug
            ];

            $lines[] = $content;
        }

        return ['headers' => $headers, 'lines' => $lines];
    }
}