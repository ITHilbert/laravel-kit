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
- Vue Componente für Tooltips (i)
- hform überarbeiten bezüglich Token lesen
