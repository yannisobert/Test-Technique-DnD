<?php

namespace App\Service;

use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Read
{
    private $format;

    function __construct(Format $format)
    {
        $this->format = $format;
    }

    public function csvToArray(string $productCsv, InputInterface $input, OutputInterface $output, $io, $json) {
        $csvOpen = fopen($productCsv, 'rb');

        $true = true;
        $table = new Table($output);
        $array = [];

        // Loop to create the table
        while (($document = fgetcsv($csvOpen, 1000, ';')) !== false) {
            if ($true) {
                // Checking table entries
                for ($i = 0; $i<=6; $i++) {
                    if (isset($document[$i])) {
                        continue;
                    }  else {
                        $io->warning('There is an error in the csv file.');
                        return 1;
                    }
                }

                // Set headers of the table
                $table->setHeaders([$document[0], 'Status', $document[3], $document[5], 'Created At', 'Slug']);
                $true = false;

                $titleArray = [$document];

                continue;
            } else {
                $table->addRow(new TableSeparator());
            }

            // For the formatting rules
            $date = new \DateTime($document[6]);
            $slug = preg_replace('/[^A-Za-z0-9\-]/', '-', str_replace(' ', '_',
                strtolower($document[1])));
            $price = str_replace('.', ',', $document[3]);
            $currency = strip_tags($document[4]);
            $description = str_replace('<br/>', "\n",str_replace('\r', "\n",
                $document[5]));

            // Set row of the table
            $table->addRow([$document[0], ($document[2] ? 'Enable' : 'Disable'), $price . $currency, $description,
                $date->format('l, j-M-Y H:i:s T'), $slug]);

            $content = [
                $titleArray[0][0] =>$document[0],
                $titleArray[0][3] => $price . $currency,
                'Status' => $document[2] ? 'Enable' : 'Disable',
                $titleArray[0][5] => $document[5],
                'Created At' => $date->format('l, j-M-Y H:i:s T'),
                'Slug' => $slug
            ];

            array_push($array, $content);
        }


        if (!$json) {
            $table->render();

            $io->success('DONE.');
        }

        // Format
        $this->format->formatter($input, $array, $io);


        return 0;
    }
}