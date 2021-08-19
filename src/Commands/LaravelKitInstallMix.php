<?php
namespace ITHilbert\LaravelKit\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\ComposerScripts;


class LaravelKitInstallMix extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'install:mix';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Installiert NPM Komponenten für mix';

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
        $this->info('Install NPM Ressources');
        exec('npm install jquery');
        exec('npm install jquery-ui');
        //exec('npm install webpack-jquery-ui');
        $this->info('jquery Okay');

        exec('npm install datatables.net');
        exec('npm install datatables.net-dt');
        exec('npm install datatables.net-buttons');
        $this->info('DataTables Okay');

        exec('npm install admin-lte@^3.1 --save');
        $this->info('AdminLTE 3.1 Okay');

        exec('npm install bootstrap');
        $this->info('Bootstrap Okay');

        exec('npm install overlayscrollbars');
        $this->info('Overlay Scrollbars Okay');

        exec('npm install vue@^2.6.14');
        //exec('npm install vue-loader');
       // exec('npm install tinymce/tinymce-vue');
        $this->info('VUE.js Okay');

        exec('npm i @ithilbert/jlib');
        $this->info('jlib Okay');

        return 0;
    }
}
