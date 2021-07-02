<?php

namespace ITHilbert\LaravelKit\App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\ComposerScripts;


class LaravelKitInstallAdminLte extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'install:AdminLte';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Installiert das Template AdminLte';

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
        //Template Adminlte
        $this->info('Install jeroennoten/laravel-adminlte');
        exec('composer require jeroennoten/laravel-adminlte');
        exec('php artisan adminlte:install');
        exec('php artisan adminlte:install --only=main_views');  //Copy Views

        return 0;
    }
}
