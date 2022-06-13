<?php

namespace App\Command;

use App\Service\Output;
use App\Service\Reader;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ProductCommand extends Command
{
    protected static $defaultName = 'app:csv-information';

    private $projectDir;
    private $reader;
    private $output;

    public function __construct(string $projectDir, Reader $reader, Output $output)
    {
        $this->projectDir = $projectDir;
        $this->reader = $reader;
        $this->output = $output;

        parent::__construct();
    }

    public function configure(): void
    {
        $this->setDescription('Description.')
            ->addArgument('path', InputArgument::REQUIRED, 'csv path.')
            ->addArgument('json', InputArgument::OPTIONAL, 'json.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        // Argument retrieval
        $pathCsv = $input->getArgument('path');
        $json = $input->getArgument('json');

        // Verification if we have a third argument
        if ($json) {
            // Verification if the third argument is good
            if ($json !== 'json') {
                $io->warning('The third argument doesn\'t exist, please retry.');

                return 1;
            }
        }

        // Path to the CSV
        $filePath = $this->projectDir . $pathCsv;

        // Verification if file exist
        if (!is_file($filePath)) {
            $io->error('File not found');

            return 1;
        }

        // Read file then output data
        try {
            $result = $this->reader->csvToArray($filePath);

            if ($json) {
                echo $this->output->toJson($result['lines']);
            } else {
                $table = new Table($output);
                $table = $this->output->toTable($result, $table);
                $table->render();
            }

            return 0;

        } catch (\Exception $exception) {
            $io->error($exception->getMessage());

            return 1;
        }
    }
}