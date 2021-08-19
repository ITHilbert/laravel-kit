<?php

namespace ITHilbert\LaravelKit\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\ComposerScripts;


class LaravelKitInstallCustomer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'install:customer';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Installiert meine Customer Komponente';

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
        $this->info('Install ithilbert/customer');
        exec('composer require ithilbert/customer');
        exec('php artisan customer:copyfiles');

        return 0;
    }
}
