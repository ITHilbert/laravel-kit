# Laravel-Kit

## Installation
```
//Laravel-Kit installieren
composer require ithilbert/laravel-kit:dev-master


//copy config file
php artisan vendor:publish --provider="ITHilbert\LaravelKit\LaravelKitServiceProvider" --tag=config 

/config/app.php
providers:
"ITHilbert\\LaravelKit\\LaravelKitServiceProvider"

alias:
'DataTableScript' => \ITHilbert\LaravelKit\Helpers\DataTableScript::class,
```

## Im Template

### Body end
```
<!-- Wichtig zum löschen von Daten -->
@include('include.formdelete')
```

### Componenten
x-laravelkit-agb
x-laravelkit-cookies
x-laravelkit-datenschutz
x-laravelkit-impressum


### ToDo


### Links

[Laravel Packages](https://laravelpackage.com/) 
[Icons - material-icons](https://materializecss.com/icons.html) 
[Mailtrap - Mail testen](https://mailtrap.io) 




"extra": {
        "laravel": {
            "providers": [
                "ITHilbert\\LaravelKit\\LaravelKitServiceProvider",
                "Yajra\\DataTables\\DataTablesServiceProvider",
                "Orangehill\\Iseed\\IseedServiceProvider",
                "Barryvdh\\Debugbar\\ServiceProvider"
            ],
            "aliases": {
                "DataTables": "Yajra\\DataTables\\Facades\\DataTables",
                "Debugbar": "Barryvdh\\Debugbar\\Facade"
            }
        }
    }
