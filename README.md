# Laravel-Kit

## Installation
```
//Laravel-Kit installieren
composer require ithilbert/laravel-kit

//config DB connection in .env

//config/app.php set
'locale' => 'de',

//Componenten installieren und Dateien kopieren
php artisan laravelkit:install

php artisan vendor:publish --provider="ITHilbert\LaravelKit\LaravelKitServiceProvider" --force


/config/app.php
providers:
"ITHilbert\\LaravelKit\\LaravelKitServiceProvider"

alias:
"HButton": "ITHilbert\\LaravelKit\\Helpers\\HButton",
"HForm": "ITHilbert\\LaravelKit\\Helpers\\HForm"

## Im Template
### Header
```
{{-- Base Stylesheets --}}
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/vue/vuecomponents.css') }}">

```

### Body end
```
<!-- Wichtig zum löschen von Daten -->
@include('include.formdelete')

{{-- Base Scripts --}}
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('vendor/laravelkit/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('vendor/vue/vuecomponents.js') }}"></script>
    <script src="{{ asset('vendor/laravelkit/datatables.min.js') }}"></script>
    <script src="{{ asset('vendor/laravelkit/myFunctions.js') }}"></script>
```

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
