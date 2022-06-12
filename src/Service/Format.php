<?php

namespace App\Service;
use Symfony\Component\Console\Input\InputInterface;

class Format
{
    public function formatter(InputInterface $input, array $array, $io) {

        // Choice enter the json & the table
        if ($input->getArgument('json') === 'json') {
            $json = json_encode($array, JSON_PRETTY_PRINT);

            echo $json;
            $io->success('DONE.');
        }
    }
}