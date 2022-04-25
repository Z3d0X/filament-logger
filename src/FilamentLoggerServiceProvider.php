<?php

namespace Z3d0X\FilamentLogger;

use Filament\Facades\Filament;
use Filament\PluginServiceProvider;
use Illuminate\Auth\Events\Login;
use Illuminate\Notifications\Events\NotificationFailed;
use Illuminate\Notifications\Events\NotificationSent;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
use Spatie\LaravelPackageTools\Package;
use Z3d0X\FilamentLogger\Commands\FilamentLoggerCommand;

class FilamentLoggerServiceProvider extends PluginServiceProvider
{

    public static string $name = 'filament-logger';

    public function registeringPackage(): void
    {
        $this->app->register(FilamentLoggerEventServiceProvider::class);
    }

    public function packageBooted(): void
    {
        parent::packageBooted();

        if (config('filament-logger.resources.enabled', true)) {
            $exceptResources = config('filament-logger.resources.exclude');
            $removedExcludedResources = collect(Filament::getResources())->filter(function ($resource) use ($exceptResources) {
                return ! in_array(Str::of($resource)->afterLast('\\'), $exceptResources);
            });

            foreach ($removedExcludedResources as $resource) {
                $models[] = $resource::getModel()::observe(config('filament-logger.resources.logger'));
            }
        }
    }
}
