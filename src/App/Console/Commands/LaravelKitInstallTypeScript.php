<?php

namespace ITHilbert\LaravelKit\App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\ComposerScripts;


class LaravelKitInstallTypeScript extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'install:typescript';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Installiert meine TypeScript funktionen';

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
        //TypeScript
        $this->info('Install ithilbert/typescript');
        exec('composer require ithilbert/typescript');
        exec('php artisan typescript:copyfiles');

        return 0;
    }
}
