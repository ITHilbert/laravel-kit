<?php

namespace ITHilbert\LaravelKit\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\ComposerScripts;


class LaravelKitInstallSite extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'install:site';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Installiert Site als neues Packages';

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
        $this->info('Install ithilbert/site');
        exec('git clone https://github.com/ITHilbert/Site.git ./packages/site');
        $this->info('Composer.json um folgende im Punkt autoload -> psr-4 erweitern');
        $this->info('"ITHilbert\\Site\\": "src/"');

        return 0;
    }
}
