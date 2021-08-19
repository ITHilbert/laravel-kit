<?php
namespace ITHilbert\LaravelKit\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\ComposerScripts;


class LaravelKitInstallUserAuth extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'install:userauth';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Installiert UserAuth';

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
        //UserAuth
        $this->info('Install ithilbert/user-auth');
        exec('composer require ithilbert/user-auth:dev-master');
        exec('php artisan vendor:publish --provider="ITHilbert\UserAuth\UserAuthServiceProvider"');

        return 0;
    }
}
