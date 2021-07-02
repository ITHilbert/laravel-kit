<?php

namespace ITHilbert\LaravelKit;

use Illuminate\Support\ServiceProvider;

class LaravelKitServiceProvider extends ServiceProvider
{
    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerCommands();

        $this->loadMigrationsFrom(__DIR__ . '/Database/Migrations');

        //Config files
        $this->publishes([
            __DIR__ .'/Config/database.php' => config_path('database.php')
        ]);

        //Public files
        $this->publishes([
            __DIR__ .'/Public/DataTable_DE.json' => public_path('DataTable_DE.json'),
            __DIR__ .'/Public/images' => public_path('images'),
            __DIR__ .'/Public/fonts' => public_path('fonts'),
        ]);

        //Lang Files
        $this->publishes([
            __DIR__.'/Resources/lang/de/master.php' => resource_path('lang/de/master.php'),
            __DIR__.'/Resources/lang/de/pagination.php' => resource_path('lang/de/pagination.php'),
            __DIR__.'/Resources/lang/de/validation.php' => resource_path('lang/de/validation.php'),
        ]);

        //Views
        $this->publishes([
            __DIR__.'/Resources/views/include/formdelete.blade.php' => resource_path('views/include/formdelete.blade.php'),
            __DIR__.'/Resources/views/include/message.blade.php' => resource_path('views/include/message.blade.php')
        ]);
    }


    /**
     * Register commands.
     *
     * @return void
     */
    protected function registerCommands()
    {
        $this->commands( \ITHilbert\LaravelKit\App\Console\Commands\LaravelKitPaths::class );
        $this->commands( \ITHilbert\LaravelKit\App\Console\Commands\LaravelKitCopyFiles::class );
        $this->commands( \ITHilbert\LaravelKit\App\Console\Commands\LaravelKitInstallAdminLte::class );
        $this->commands( \ITHilbert\LaravelKit\App\Console\Commands\LaravelKitInstallAll::class );
        $this->commands( \ITHilbert\LaravelKit\App\Console\Commands\LaravelKitInstallDataTables::class );
        $this->commands( \ITHilbert\LaravelKit\App\Console\Commands\LaravelKitInstallDebugbar::class );
        $this->commands( \ITHilbert\LaravelKit\App\Console\Commands\LaravelKitInstalliSeed::class );
    }



}
