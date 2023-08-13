<?php

namespace Z3d0X\FilamentLogger;

use Filament\Facades\Filament;
use Filament\Panel;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;

class FilamentLoggerServiceProvider extends PackageServiceProvider
{
    public static string $name = 'filament-logger';

    protected function getResources(): array
    {
        return [
            config('filament-logger.activity_resource')
        ];
    }

    public function configurePackage(Package $package): void
    {
        $package->name(static::$name)
            ->hasTranslations()
            ->hasConfigFile()
            ->hasInstallCommand(function (InstallCommand $installCommand) {
                $installCommand
                    ->publishConfigFile()
                    ->askToStarRepoOnGitHub('z3d0x/filament-logger')
                    ->startWith(function (InstallCommand $installCommand) {
                        $installCommand->call('vendor:publish', [
                            '--provider' => "Spatie\Activitylog\ActivitylogServiceProvider",
                            '--tag' => "activitylog-migrations"
                        ]);
                    });
            });
    }

    public function registeringPackage(): void
    {
        $this->app->register(FilamentLoggerEventServiceProvider::class);
    }

    public function packageBooted(): void
    {
        parent::packageBooted();

        if (config('filament-logger.resources.enabled', true)) {
            $exceptResources = [...config('filament-logger.resources.exclude'), config('filament-logger.activity_resource')];

            $loggableResources = collect(Filament::getPanels())
                ->flatMap(fn (Panel $panel) => $panel->getResources())
                ->unique()
                ->filter(function ($resource) use ($exceptResources) {
                    return ! in_array($resource, $exceptResources);
                });

            foreach ($loggableResources as $resource) {
                $resource::getModel()::observe(config('filament-logger.resources.logger'));
            }
        }

        if (config('filament-logger.models.enabled', true) && ! empty(config('filament-logger.models.register'))) {
            foreach (config('filament-logger.models.register', []) as $model) {
                $model::observe(config('filament-logger.models.logger'));
            }
        }
    }
}
