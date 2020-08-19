<?php

namespace ITHilbert\LaravelKit\App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\ComposerScripts;


class paths extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'laravelkit:paths';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Zeigt verschiedene Pfade an';

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
        $this->info('pwd: ' . exec('pwd'));
        $this->info('base_path: ' . base_path());
        $this->info('app_path: ' . app_path());
        $this->info('config_path: ' . config_path());
        $this->info('storage_path: ' . storage_path() );
        $this->info('__DIR__: ' . __DIR__ );
        $this->info('$this->package_path: ' . $this->package_path() );

        return 0;
    }

    public function package_path(){
        $path = __DIR__;
        $path = str_replace('/App/Console/Commands', '', $path);
        return $path;
    }

}
