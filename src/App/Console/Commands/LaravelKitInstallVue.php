<?php

namespace ITHilbert\LaravelKit\App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\ComposerScripts;


class LaravelKitInstallVue extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'install:vue';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Installiert meine Vue Komponenten';

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
        $this->info('Install ithilbert/vue');
        exec('composer require ithilbert/vue');
        exec('php artisan vue:copyfiles');

        return 0;
    }
}
