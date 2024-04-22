<?php

namespace Z3d0X\FilamentLogger;

use Filament\Contracts\Plugin;
use Filament\Panel;

class LoggerPlugin implements Plugin
{
    const ID = 'z3d0x::filament-logger';

    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return static::ID;
    }

    public function boot(Panel $panel): void
    {

    }

    public function register(Panel $panel): void
    {
        $panel->resources(array_filter([
            config('filament-logger.activity_resource')
        ]));
    }

    public function navigationGroup(?string $navigationGroup): static
    {
        $this->getActivityResourceClass()::navigationGroup($navigationGroup);

        return $this;
    }

    public function navigationIcon(?string $navigationIcon): static
    {
        $this->getActivityResourceClass()::navigationIcon($navigationIcon);

        return $this;
    }

    public function navigationSort(?int $navigationSort): static
    {
        $this->getActivityResourceClass()::navigationSort($navigationSort);

        return $this;
    }

    public static function getActivityResourceClass(): string
    {
        return config('filament-logger.activity_resource');
    }

    public static function get(): static
    {
        /** @var static $plugin */
        $plugin = filament(app(static::class)->getId());

        return $plugin;
    }
}
