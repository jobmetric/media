# Media for laravel

This package is for keeping the files of different Laravel projects.

## Install via composer

Run the following command to pull in the latest version:
```bash
composer require jobmetric/media
```

### Add service provider

Add the service provider to the providers array in the config/app.php config file as follows:

```php
'providers' => [

    ...

    JobMetric\Media\Providers\MediaServiceProvider::class,
]
```

### Publish the config
Copy the `config` file from `vendor/jobmetric/media/config/config.php` to `config` folder of your Laravel application and rename it to `media.php`

Run the following command to publish the package config file:

```bash
php artisan vendor:publish --provider="JobMetric\Media\Providers\MediaServiceProvider" --tag="media-config"
```

You should now have a `config/media.php` file that allows you to configure the basics of this package.

### Publish Migrations

You need to publish the migration to create the `medias` table:

```php
php artisan vendor:publish --provider="JobMetric\Media\Providers\MediaServiceProvider" --tag="media-migrations"
```

After that, you need to run migrations.

```php
php artisan migrate
```

## Documentation
