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
###Header
```
<link rel="stylesheet" href="{{ asset('css/app.css') }}">


<script>
	var Laravel = {
	    'csrfToken' : '{{csrf_token()}}'
	};
</script>

<script src="{{ asset('js/jquery.min.js') }}"></script>
<script src="{{ asset('js/jquery-ui.min.js') }}"></script>
<script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
```

### Body end
```
<!-- Wichtig zum löschen von Daten -->
@include('include.formdelete')

<script src="{{ asset('js/app.js') }}"></script>
<script src="{{ asset('js/datatables.min.js') }}"></script>
<script src="{{ asset('vendor/laravelkit/js/deleteform.js') }}"></script>
```

### ToDo
- edit master js, css ...
- Copy master
- Vue Componente für Tooltips (i)
- hform überarbeiten bezüglich Token lesen


### Links

[Laravel Packages](https://laravelpackage.com/) 
[Icons - material-icons](https://materializecss.com/icons.html) 
[Mailtrap - Mail testen](https://mailtrap.io) 


