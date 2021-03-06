# Activity logger for filament

[![Latest Version on Packagist](https://img.shields.io/packagist/v/z3d0x/filament-logger.svg?style=flat-square)](https://packagist.org/packages/z3d0x/filament-logger)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/z3d0x/filament-logger/run-tests?label=tests)](https://github.com/z3d0x/filament-logger/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/z3d0x/filament-logger/Check%20&%20fix%20styling?label=code%20style)](https://github.com/z3d0x/filament-logger/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/z3d0x/filament-logger.svg?style=flat-square)](https://packagist.org/packages/z3d0x/filament-logger)

Configurable activity logger for filament.
Powered by `spatie/laravel-activitylog`

## Features
You can choose what you want to log and how to log it.
- Log Filament Resource Events
- Log Login Event
- Log Notification Events
- Log Model Events
- Easily extendable to log custom events

Note: By default this package will log Filament Resource Events, Access(Login) Events, and Notification Events. If you want to log a model that is not a FilamentResource you will have to manually register in the config file.
## Installation

This package uses [spatie/laravel-activitylog](https://spatie.be/docs/laravel-activitylog), instructions for its setup can be found [here](https://spatie.be/docs/laravel-activitylog/v4/installation-and-setup)

You can install the package via composer:

```bash
composer require z3d0x/filament-logger
```
You can publish the config file with:

```bash
php artisan vendor:publish --tag="filament-logger-config"
```

This is the contents of the published config file:

```php
<?php
return [
    'activity_resource' => \Z3d0X\FilamentLogger\Resources\ActivityResource::class,

    'resources' => [
        'enabled' => true,
        'log_name' => 'Resource',
        'logger' => \Z3d0X\FilamentLogger\Loggers\ResourceLogger::class,
        'color' => 'success',
        'exclude' => [
            //App\Filament\Resources\UserResource::class,
        ],
    ],

    'access' => [
        'enabled' => true,
        'logger' => \Z3d0X\FilamentLogger\Loggers\AccessLogger::class,
        'color' => 'danger',
        'log_name' => 'Access',
    ],

    'notifications' => [
        'enabled' => true,
        'logger' => \Z3d0X\FilamentLogger\Loggers\NotificationLogger::class,
        'color' => null,
        'log_name' => 'Notification',
    ],

    'models' => [
        'enabled' => true,
        'log_name' => 'Model',
        'color' => 'warning',
        'logger' => \Z3d0X\FilamentLogger\Loggers\ModelLogger::class,
        'register' => [
            //App\Models\User::class,
        ],
    ],

    'custom' => [
        // [
        //     'log_name' => 'Custom',
        //     'color' => 'primary',
        // ]
    ]
];
```
## Future Scope
- Log `spatie/laravel-settings`

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](https://github.com/spatie/.github/blob/main/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Ziyaan Hassan](https://github.com/Z3d0X)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
