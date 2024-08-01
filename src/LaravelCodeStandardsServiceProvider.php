<?php

namespace Saade\LaravelCodeStandards;

use Saade\LaravelCodeStandards\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaravelCodeStandardsServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-code-standards')
            ->hasCommand(InstallCommand::class);
    }
}
