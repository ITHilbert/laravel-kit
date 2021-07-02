<?php

namespace ITHilbert\LaravelKit\App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\ComposerScripts;


class LaravelKitInstall extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'laravelkit:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Installiert Komponenten für ein neues Project';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        //ISeed
        $this->info('Install orangehill/iseed');
        exec('composer require orangehill/iseed');

        //Datatables
        $this->info('Install yajra/laravel-datatables-oracle');
        exec('composer require yajra/laravel-datatables-oracle:"~9.0"');
        exec('php artisan vendor:publish --provider="Yajra\DataTables\DataTablesServiceProvider"');

        //Debugbar
        $this->info('Install barryvdh/laravel-debugbar');
        exec('composer require barryvdh/laravel-debugbar --dev');
        exec('php artisan vendor:publish --provider="Barryvdh\\Debugbar\\ServiceProvider"');

        //Template Adminlte
        $this->info('Install jeroennoten/laravel-adminlte');
        exec('composer require jeroennoten/laravel-adminlte');
        exec('php artisan adminlte:install');
        exec('php artisan adminlte:install --only=main_views');  //Copy Views

        //UserAuth
        $this->info('Install ithilbert/user-auth');
        exec('composer require ithilbert/user-auth');
        exec('php artisan vendor:publish --provider="ITHilbert\UserAuth\UserAuthServiceProvider"');


        //LaravelKit Dateien kopieren
        exec('php artisan vendor:publish --provider="ITHilbert\LaravelKit\LaravelKitServiceProvider" --force');

        exec('php artisan migrate');
        exec('php artisan db:seed --class="ITHilbert\UserAuth\Database\Seeders\DatabaseSeeder"');

        return 0;
    }
}
