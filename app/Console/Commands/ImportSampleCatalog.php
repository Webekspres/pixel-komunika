<?php

namespace App\Console\Commands;

use App\Domains\SeedDataSupport\SampleCatalogImporter;
use Illuminate\Console\Command;

class ImportSampleCatalog extends Command
{
    protected $signature = 'catalog:import-sample';

    protected $description = 'Import sample POS-like catalog data for development and UAT';

    public function handle(SampleCatalogImporter $importer): int
    {
        $importer->import();

        $this->components->info('Sample catalog imported.');

        return self::SUCCESS;
    }
}
