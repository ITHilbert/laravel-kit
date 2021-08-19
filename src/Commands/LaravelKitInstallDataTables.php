<?php

namespace ITHilbert\LaravelKit\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\ComposerScripts;


class LaravelKitInstallDataTables extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'install:DataTables';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Installiert DataTables';

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
        //Datatables
        $this->info('Install yajra/laravel-datatables-oracle');
        exec('composer require yajra/laravel-datatables-oracle:"~9.0"');
        exec('php artisan vendor:publish --provider="Yajra\DataTables\DataTablesServiceProvider"');

        return 0;
    }
}
