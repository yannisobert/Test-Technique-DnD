<?php

namespace App\Service;

use Symfony\Component\Console\Helper\Table;

class Output
{
    public function toJson(array $content): string
    {
        return json_encode($content, JSON_PRETTY_PRINT);
    }

    public function toTable(array $content, Table $table): Table
    {
        $table->setHeaders($content['headers']);
        $table->setRows($content['lines']);
        return $table;
    }
}