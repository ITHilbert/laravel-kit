# Laravel-Kit

## Laravel Components

- **[Laravel UI](https://github.com/laravel/ui)**

- **[DataTables](https://datatables.net/)**

- **[Inverse seed generator (iSeed)](https://github.com/orangehill/iseed)**

- **[Laravel-Debugbar](https://github.com/barryvdh/laravel-debugbar)**


## Template

- **[AdminLTE](https://github.com/jeroennoten/Laravel-AdminLTE)**


## Installation
```
//Laravel-Kit installieren
composer require ithilbert/laravel-kit

//config DB connection in .env

//config/app.php set
'locale' => 'de',

//Componenten installieren und Dateien kopieren
php artisan laravelkit:install

----
Alt
----

//Dateien kopieren
php artisan vendor:publish --provider="ITHilbert\LaravelKit\LaravelKitServiceProvider"

//Tabellen erstellen
php artisan migrate

//Daten einspielen
php artisan db:seed --class="ITHilbert\LaravelKit\Database\Seeders\DatabaseSeeder"

```

## Im Template
### Header
```
{{-- Base Stylesheets --}}
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/laravelkit/css/vuecomponents.css') }}">

```

### Body end
```
<!-- Wichtig zum löschen von Daten -->
@include('include.formdelete')

{{-- Base Scripts --}}
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('vendor/laravelkit/js/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('vendor/laravelkit/js/vuecomponents.js') }}"></script>
    <script src="{{ asset('vendor/laravelkit/js/datatables.min.js') }}"></script>
    <script src="{{ asset('vendor/laravelkit/js/myFunctions.js') }}"></script>
```

### ToDo

- Vue Componente für Tooltips (i)


### Links

[Laravel Packages](https://laravelpackage.com/) 
[Icons - material-icons](https://materializecss.com/icons.html) 
[Mailtrap - Mail testen](https://mailtrap.io) 


