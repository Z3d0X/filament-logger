<?php

namespace Z3d0X\FilamentLogger;

use Closure;
use Filament\Contracts\Plugin;
use Filament\Panel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Spatie\Activitylog\Models\Activity;

class LoggerPlugin implements Plugin
{
    const ID = 'z3d0x::filament-logger';

    protected ?Closure $resolveSubjectNameUsing = null;

    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return static::ID;
    }

    public static function get(): static
    {
        /** @var static $plugin */
        $plugin = filament(app(static::class)->getId());

        return $plugin;
    }

    public function register(Panel $panel): void
    {
        $panel->resources(array_filter([
            config('filament-logger.activity_resource')
        ]));
    }

    public function boot(Panel $panel): void
    {

    }

    public static function getActivityResourceClass(): string
    {
        return config('filament-logger.activity_resource');
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

    public function resolveSubjectNameUsing(?Closure $callback): static
    {
        $this->resolveSubjectNameUsing = $callback;

        return $this;
    }

    public function getResolveSubjectNameUsing(): ?Closure
    {
        if (! is_null($this->resolveSubjectNameUsing)) {
            return $this->resolveSubjectNameUsing;
        };

        return static function ($state, Model $record) {
            /** @var Activity $record */
            if (!$state) {

                return '-';
            }

            return Str::of($state)->afterLast('\\')->headline().' # '.$record->subject_id;
        };
    }
}
