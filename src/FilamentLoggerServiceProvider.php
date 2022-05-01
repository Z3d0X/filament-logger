<?php

namespace Z3d0X\FilamentLogger;

use Filament\Facades\Filament;
use Filament\PluginServiceProvider;

class FilamentLoggerServiceProvider extends PluginServiceProvider
{

    public static string $name = 'filament-logger';

    protected function getResources(): array
    {
        return [
            config('filament-logger.activity_resource')
        ];
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
            $removedExcludedResources = collect(Filament::getResources())->filter(function ($resource) use ($exceptResources) {
                return ! in_array($resource, $exceptResources);
            });

            foreach ($removedExcludedResources as $resource) {
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
