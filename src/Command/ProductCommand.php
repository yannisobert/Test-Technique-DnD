<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;


class ProductCommand extends Command
{
    protected static $defaultName = 'app:csv-information';

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
        $io = new SymfonyStyle($input, $output);

        $nameFile = $input->getArgument('file');
        $test = $input->getArgument('json');

        if ($nameFile !== 'products') {
            $io->warning('The first argument doesn\'t exist, please retry.');

            return 0;
        }

        if ($test)
        {
            if ($test !== 'json') {
                $io->warning('The second argument doesn\'t exist, please retry.');

                return 0;
            }
        }

        $productCsv = $this->projectDir . '/public/csv/' . $nameFile . '.csv';

        $csvOpen = fopen($productCsv, 'rb');

        $true = true;

        $table = new Table($output);

        $array = [];

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
            $currency = strip_tags($document[4]);

            $description = str_replace('<br/>', "\n",str_replace('\r', "\n", $document[5]));

            $table->addRow([$document[0], ($document[2] ? 'Enable' : 'Disable'), $price . $currency, $description, $date->format('l, j-M-Y H:i:s T'), $slug]);

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

        $json = json_encode($array, JSON_PRETTY_PRINT);

        if ($input->getArgument('json') === 'json') {
            echo $json;
        } else {
            $table->render();
        }

        $io->success('DONE.');
    }
}