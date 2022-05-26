<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class ProductCommand extends Command
{
    protected static $defaultName = 'app:csv-information';
    private $dataProcess;

    function __construct(
        $projectDir
    )
    {
        $this->projectDir = $projectDir;

        parent::__construct();
    }

    function configure()
    {
        $this->setDescription('Description.')
            ->addArgument('file', InputArgument::REQUIRED, 'csv name.')
            ->addArgument('json', InputArgument::OPTIONAL, 'json.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $nameFile = $input->getArgument('file');
        $json = $input->getArgument('json');

        $productCsv = $this->projectDir . '/public/csv/' . $nameFile . '.csv';

        $csvOpen = fopen($productCsv, 'rb');

        $true = true;

        $table = new Table($output);

        $array = [];
        $titleArray = [];

        while (($document = fgetcsv($csvOpen, 1000, ';')) !== false) {
            if ($true) {

                $table->setHeaders([$document[0], 'Status', $document[3], $document[5], 'Created At', 'Slug']);
                $true = false;

                $titleArray = [$document];


                continue;
            } else {
                $table->addRow(new TableSeparator());
            }


            $date = new \DateTime($document[6]);
            $slug = preg_replace('/[^A-Za-z0-9\-]/', '-', str_replace(' ', '_', strtolower($document[1])));
            $price = str_replace('.', ',', $document[3]);
            $description = strip_tags($document[4]);

            $table->addRow([$document[0], ($document[2] ? 'Enable' : 'Disable'), $price . $description, $document[5], $date->format('l, j-M-Y H:i:s T'), $slug]);

            //dd($document[5]);

            $content = [
                $titleArray[0][0] =>$document[0],
                $titleArray[0][3] => $price . $description,
                'Status' => $document[2] ? 'Enable' : 'Disable',
                $titleArray[0][5] => $document[5],
                'Created At' => $date->format('l, j-M-Y H:i:s T'),
                'Slug' => $slug
            ];

            array_push($array, $content);
        }

        $json = json_encode($array, JSON_PRETTY_PRINT);

        echo $json;


        $table->render();
    }
}
