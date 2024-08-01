<?php

namespace Saade\LaravelCodeStandards\Commands;

use Illuminate\Console\Command;

class InstallCommand extends Command
{
    public $signature = 'code-standards:install';

    public $description = 'Install the Laravel Code Standards';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
