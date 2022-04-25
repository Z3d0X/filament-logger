# Activity logger for filament

[![Latest Version on Packagist](https://img.shields.io/packagist/v/z3d0x/filament-logger.svg?style=flat-square)](https://packagist.org/packages/z3d0x/filament-logger)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/z3d0x/filament-logger/run-tests?label=tests)](https://github.com/z3d0x/filament-logger/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/z3d0x/filament-logger/Check%20&%20fix%20styling?label=code%20style)](https://github.com/z3d0x/filament-logger/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/z3d0x/filament-logger.svg?style=flat-square)](https://packagist.org/packages/z3d0x/filament-logger)

Configurable activity logger for filament.
Powered by `spatie/laravel-activitylog`

## Features
You can choose what you want to log.
- Log Resource(Model) Events
- Log Login Event
- Log Notification Events
- Easily extendable to log custom model events
## Installation

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
    'resources' => [
        'enabled' => true,
        'log_name' => 'Resource',
        'logger' => \Z3d0X\FilamentLogger\Loggers\ResourceLogger::class,
        'color' => 'success',
        'exclude' => [
            //UserResource::class,
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

    'custom' => [
        // [
        //     'name' => 'Custom',
        //     'color' => 'primary',
        // ]
    ]
];
```
## Future Scope
- Log `spatie/laravel-settings`
- Publishable `ActivityResource`

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
