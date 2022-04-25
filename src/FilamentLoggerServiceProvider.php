<?php

namespace Z3d0X\FilamentLogger;

use Filament\Facades\Filament;
use Filament\PluginServiceProvider;
use Z3d0X\FilamentLogger\Resources\ActivityResource;

class FilamentLoggerServiceProvider extends PluginServiceProvider
{

    public static string $name = 'filament-logger';

    protected array $resources = [
        ActivityResource::class,
    ];

    public function registeringPackage(): void
    {
        $this->app->register(FilamentLoggerEventServiceProvider::class);
    }

    public function packageBooted(): void
    {
        parent::packageBooted();

        if (config('filament-logger.resources.enabled', true)) {
            $exceptResources = [...config('filament-logger.resources.exclude'), ActivityResource::class];
            $removedExcludedResources = collect(Filament::getResources())->filter(function ($resource) use ($exceptResources) {
                return ! in_array($resource, $exceptResources);
            });

            foreach ($removedExcludedResources as $resource) {
                $models[] = $resource::getModel()::observe(config('filament-logger.resources.logger'));
            }
        }
    }
}
