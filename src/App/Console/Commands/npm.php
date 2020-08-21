<?php

namespace ITHilbert\LaravelKit\App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\ComposerScripts;


class npm extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'laravelkit:npm';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Installiert nötige npm Komponenten';

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
        //Laravel UI
        $this->info('laravel-mix');

        //LaravelKit Dateien kopieren
        exec('npm install laravel-mix');

        return 0;
    }
}
