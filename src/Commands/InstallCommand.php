<?php

namespace Saade\LaravelCodeStandards\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\Console\InteractsWithComposerPackages;
use Illuminate\Support\Facades\Process;

use function Laravel\Prompts\confirm;

class InstallCommand extends Command
{
    use InteractsWithComposerPackages;

    public $signature = 'code-standards:install
                    {--composer=global : Absolute path to the Composer binary which should be used to install packages}
                    {--force : Overwrite any existing files}
                    {--without-composer : Do not prompt to install Composer dependencies}
                    {--without-node : Do not prompt to install Node dependencies}';

    public $description = 'Install the Laravel Code Standards';

    public function handle(): int
    {
        // .prettierignore
        if (! file_exists($prettierIgnorePath = $this->laravel->basePath('.prettierignore')) || $this->option('force')) {
            $this->components->info('Published .prettierignore file.');

            copy(__DIR__.'/../../stubs/.prettierignore.stub', $prettierIgnorePath);
        }

        // .prettierrc
        if (! file_exists($prettierRcPath = $this->laravel->basePath('.prettierrc')) || $this->option('force')) {
            $this->components->info('Published .prettierrc file.');

            copy(__DIR__.'/../../stubs/.prettierrc.stub', $prettierRcPath);
        }

        // php-cs-fixer.php
        if (! file_exists($phpCsFixerPath = $this->laravel->basePath('php-cs-fixer.php')) || $this->option('force')) {
            $this->components->info('Published php-cs-fixer.php file.');

            copy(__DIR__.'/../../stubs/php-cs-fixer.php.stub', $phpCsFixerPath);
        }

        // phpstan-baseline.neon
        if (! file_exists($phpStanBaselinePath = $this->laravel->basePath('phpstan-baseline.neon')) || $this->option('force')) {
            $this->components->info('Published phpstan-baseline.neon file.');

            copy(__DIR__.'/../../stubs/phpstan-baseline.neon.stub', $phpStanBaselinePath);
        }

        // phpstan.neon.dist
        if (! file_exists($phpStanPath = $this->laravel->basePath('phpstan.neon.dist')) || $this->option('force')) {
            $this->components->info('Published phpstan.neon.dist file.');

            copy(__DIR__.'/../../stubs/phpstan.neon.dist.stub', $phpStanPath);
        }

        // rector.php
        if (! file_exists($rectorPath = $this->laravel->basePath('rector.php')) || $this->option('force')) {
            $this->components->info('Published rector.php file.');

            copy(__DIR__.'/../../stubs/rector.php.stub', $rectorPath);
        }

        // composer.json
        if (file_exists($composerJsonPath = $this->laravel->basePath('composer.json'))) {
            $composerJsonContents = json_decode(file_get_contents($composerJsonPath), true);
            $composerJsonStubContents = json_decode(file_get_contents(__DIR__.'/../../stubs/composer.json.stub'), true);

            foreach ($composerJsonStubContents['scripts'] as $name => $script) {
                $composerJsonContents['scripts'][$name] = array_unique(array_merge($composerJsonContents['scripts'][$name] ?? [], $script));
            }

            file_put_contents($composerJsonPath, json_encode($composerJsonContents, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES).PHP_EOL);
        }

        $this->installComposerDependencies();
        $this->installNodeDependencies();

        return self::SUCCESS;
    }

    /**
     * Install Composer dependencies if desired.
     */
    protected function installComposerDependencies(): void
    {
        if ($this->option('without-composer') || ! confirm('Would you like to install Composer dependencies?', default: true)) {
            return;
        }

        $succeed = $this->requireComposerPackages($this->option('composer'), $packages = [
            'barryvdh/laravel-ide-helper:^3.0',
            'friendsofphp/php-cs-fixer:^3.51',
            'larastan/larastan:^2.9',
            'rector/rector:^1.0',
            'spatie/laravel-ignition',
        ]);

        if (! $succeed) {
            $this->components->warn('Composer dependencies could not be installed. Please run the following command manually:');
            echo '      composer require'.implode(' ', $packages).' --dev';
        } else {
            $this->components->info('Composer dependencies installed.');
        }
    }

    /**
     * Install Node dependencies if desired.
     */
    protected function installNodeDependencies(): void
    {
        if ($this->option('without-node') || ! confirm('Would you like to install Node dependencies?', default: true)) {
            return;
        }

        $this->components->info('Installing Node dependencies...');

        $commands = [
            'npm install @shufo/prettier-plugin-blade@^1.11.1 prettier@^3.0.2 --save-dev',
        ];

        $command = Process::command(implode(' && ', $commands))->path(base_path());

        if (! windows_os()) {
            $command->tty(true);
        }

        if ($command->run()->failed()) {
            $this->components->warn('Node dependencies could not be installed. Please run the following commands manually:');
            echo '      '.implode(' && ', $commands);
        } else {
            $this->components->info('Node dependencies installed successfully.');
        }
    }
}
