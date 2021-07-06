<?php

namespace ITHilbert\LaravelKit\App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\ComposerScripts;


class LaravelKitInstallAll extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'install:all';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Installiert alle Komponenten für ein neues Project';

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
        $this->info('Install Template AdminLte');
        $this->info('php artisan install:AdminLte');
        exec('php artisan install:AdminLte');
        //Datatables
        $this->info('Install Datatables');
        $this->info('php artisan install:DataTables');
        exec('php artisan install:DataTables');
        //Debugbar
        $this->info('Install Debugbar');
        $this->info('php artisan install:Debugbar');
        exec('php artisan install:Debugbar');
        //iSeed
        $this->info('Install iSeed');
        $this->info('php artisan install:iSeed');
        exec('php artisan install:iSeed');
        //TypeScript
        $this->info('Install ithilbert/typescript');
        $this->info('php artisan install:typescript');
        exec('php artisan install:typescript');
        //UserAuth
        $this->info('Install ithilbert/userauth');
        $this->info('php artisan install:userauth');
        exec('php artisan install:userauth');
        //Vue
        $this->info('Install ithilbert/vue');
        $this->info('php artisan install:vue');
        exec('php artisan install:vue');
        //LaravelKit Dateien kopieren
        $this->info('LaravelKit Daten kopieren');
        $this->info('php artisan install:copyfiles');
        exec('php artisan laravelkit:copyfiles');
        //Site
        $this->info('Erstelle Package Site');
        $this->info('php artisan install:site');
        exec('php artisan install:site');

        //Daten in die Datenbank eintragen
        $this->info('Daten in die Datenbank eintragen');
        $this->info('php artisan migrate');
        exec('php artisan migrate');
        $this->info('php artisan db:seed --class="ITHilbert\UserAuth\Database\Seeders\DatabaseSeeder"');
        exec('php artisan db:seed --class="ITHilbert\UserAuth\Database\Seeders\DatabaseSeeder"');

        $this->info('Composer.json um folgende im Punkt autoload -> psr-4 erweitern');
        $this->info('"ITHilbert\\Site\\": "src/"');


        return 0;
    }
}
