<?php

namespace Z3d0X\FilamentLogger;

use Illuminate\Auth\Events\Login;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Notifications\Events\NotificationFailed;
use Illuminate\Notifications\Events\NotificationSent;

class FilamentLoggerEventServiceProvider extends ServiceProvider
{
    public function listens()
    {
        $listen = array_merge(
            config('filament-logger.access.enabled') ? [
                Login::class => [
                    config('filament-logger.access.logger'),
                ],
            ] : [],
            config('filament-logger.notifications.enabled') ? [
                NotificationSent::class => [
                    config('filament-logger.notifications.logger'),
                ],
                NotificationFailed::class => [
                    config('filament-logger.notifications.logger'),
                ],
            ] : []
        );
        return $listen;
    }
}
