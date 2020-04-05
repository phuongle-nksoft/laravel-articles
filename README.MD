## Installation

Run the command below to install via Composer

```shell
composer require nksoft/laravel-articles
```

## Getting Started

### Add config file config/app.php

```php
<?php
'providers' => [
    ...
    Nksoft\Articles\NkSoftArticlesServiceProvider::class
]
```

### Migrate package

```shell
php artisan migrate
```

### DB seeder package

```shell
php artisan db:seed --class="\Nksoft\Articles\database\seeds\NksoftArticlesSeeder"
```

### Publishes file

```shell
php artisan vendor:publish --tag=nksoft --force
```

### Syslink storage file

```shell
php artisan storage:link
```

## License

Released under the MIT License attached with this code.
