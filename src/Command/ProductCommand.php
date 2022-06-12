<?php

namespace App\Command;

use App\Service\Read;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ProductCommand extends Command
{
    protected static $defaultName = 'app:csv-information';

    function __construct($projectDir, Read $read)
    {
        $this->projectDir = $projectDir;
        $this->read = $read;

        parent::__construct();
    }

    function configure()
    {
        $this->setDescription('Description.')
            ->addArgument('path', InputArgument::REQUIRED, 'csv path.')
            ->addArgument('name', InputArgument::REQUIRED, 'csv name.')
            ->addArgument('json', InputArgument::OPTIONAL, 'json.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        // Argument retrieval
        $pathCsv = $input->getArgument('path');
        $nameCsv = $input->getArgument('name');
        $json = $input->getArgument('json');

        // Verification if we have a third argument
        if ($json)
        {
            // Verification if the third argument is good
            if ($json !== 'json') {
                $io->warning('The third argument doesn\'t exist, please retry.');

                return 1;
            }
        }

        // Path to the CSV
        $productCsv = $this->projectDir . $pathCsv . $nameCsv . '.csv';

        //formatter
        $this->read->csvToArray($productCsv, $input, $output, $io, $json);

        return 0;
    }
}