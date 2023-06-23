<?php

namespace Z3d0X\FilamentLogger\Loggers;

use Filament\Facades\Filament;
use Illuminate\Auth\GenericUser;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Spatie\Activitylog\ActivityLogger;
use Spatie\Activitylog\ActivityLogStatus;

abstract class AbstractModelLogger
{
    protected abstract function getLogName(): string;

    protected function getUserName(?Authenticatable $user): string
    {
        if (blank($user) || $user instanceof GenericUser) {
            return 'Anonymous';
        }

        return Filament::getUserName($user);
    }

    protected function getModelName(Model $model)
    {
        return Str::of(class_basename($model))->headline();
    }


    protected function activityLogger(string $logName = null): ActivityLogger
    {
        $defaultLogName = $this->getLogName();

        $logStatus = app(ActivityLogStatus::class);

        return app(ActivityLogger::class)
            ->useLog($logName ?? $defaultLogName)
            ->setLogStatus($logStatus);
    }

    protected function log(Model $model, string $event, ?string $description = null, mixed $attributes = null)
    {
        if (is_null($description)) {
            $description = $this->getModelName($model) . ' ' . $event;
        }

        if (auth()->check()) {
            $description .= ' ' . __('filament-logger::filament-logger.resource.label.by') . ' ' . $this->getUserName(auth()->user());
        }

        $this->activityLogger()
            ->event($event)
            ->performedOn($model)
            ->withProperties($attributes)
            ->log($description);
    }

    public function created(Model $model)
    {
        $this->log($model, __('filament-logger::filament-logger.resource.label.created'), attributes: $model->getAttributes());
    }

    public function updated(Model $model)
    {
        $changes = $model->getChanges();

        //Ignore the changes to remember_token
        if (count($changes) === 1 && array_key_exists('remember_token', $changes)) {
            return;
        }

        $this->log($model, __('filament-logger::filament-logger.resource.label.updated'), attributes: $changes);
    }

    public function deleted(Model $model)
    {
        $this->log($model, __('filament-logger::filament-logger.resource.label.deleted'));
    }
}
