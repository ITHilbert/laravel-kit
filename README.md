# Laravel-Kit

## Installation

```
composer require ithilbert/laravel-kit

php artisan vendor:publish --provider="ITHilbert\LaravelKit\LaravelKitServiceProvider"

//Tabellen erstellen
php artisan migrate

//Daten einspielen
php artisan db:seed --class="ITHilbert\LaravelKit\Database\Seeders\DatabaseSeeder"
```
