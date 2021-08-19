<?php

namespace ITHilbert\LaravelKit\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\ComposerScripts;


class LaravelKitCopyFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'laravelkit:copyfiles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kopiert die Laravelkit Datein ins Project';

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
        $this->info('Dateien werden kopiert');

        //LaravelKit Dateien kopieren
        $erg = exec('php artisan vendor:publish --provider="ITHilbert\LaravelKit\LaravelKitServiceProvider" --force');
        $this->info($erg);
        return 0;
    }
}
