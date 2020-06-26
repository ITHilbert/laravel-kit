<?php

namespace ITHilbert\LaravelKit;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory;

class LaravelKitServiceProvider extends ServiceProvider
{
    /**
     * @var string $moduleName
     */
    protected $moduleName = 'LaravelKit';

    /**
     * @var string $moduleNameLower
     */
    protected $moduleNameLower = 'laravelkit';

    protected $loadFromPackage = true;

    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
        /* $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->registerFactories(); */
        $this->loadMigrationsFrom(__DIR__ . '/Database/Migrations');

        //$path = module_path($this->moduleName);

        //Public files
        $this->publishes([
            __DIR__ .'/Public/DataTable_DE.json' => public_path('DataTable_DE.json'),
            __DIR__ .'/Public/css' => public_path('vendor/laravelkit/css'),
            __DIR__ .'/Public/js' => public_path('vendor/laravelkit/js'),
        ]);

        //Resources Lang Files
        $this->publishes([
            __DIR__.'/Resources/lang/de/master.php' => resource_path('lang/de/master.php'),
            __DIR__.'/Resources/lang/de/pagination.php' => resource_path('lang/de/pagination.php'),
            __DIR__.'/Resources/lang/de/validation.php' => resource_path('lang/de/validation.php'),
        ]);

        //Ressources
        $this->publishes([
            __DIR__.'/Resources/views/include/formdelete.blade.php' => resource_path('views/include/formdelete.blade.php'),
            __DIR__.'/Resources/views/include/message.blade.php' => resource_path('views/include/message.blade.php'),
        ]);

        //Ressources js und sass
        $this->publishes([
            __DIR__.'/Resources/js' => resource_path('js/vendor/laravelkit'),
            __DIR__.'/Resources/scss' => resource_path('scss/vendor/laravelkit'),
        ]);

        





    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        //$this->app->register(RouteServiceProvider::class);
    }

    



    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        /* $this->publishes([
            module_path($this->moduleName, 'Config/config.php') => config_path($this->moduleNameLower . '.php'),
        ], 'config');
        $this->mergeConfigFrom(
            module_path($this->moduleName, 'Config/config.php'), $this->moduleNameLower
        ); */
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
       /*  $viewPath = resource_path('views/modules/' . $this->moduleNameLower);

        $sourcePath = module_path($this->moduleName, 'Resources/views');

        $this->publishes([
            $sourcePath => $viewPath
        ], ['views', $this->moduleNameLower . '-module-views']);

        $this->loadViewsFrom(array_merge($this->getPublishableViewPaths(), [$sourcePath]), $this->moduleNameLower); */
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        /* $langPath = resource_path('lang/modules/' . $this->moduleNameLower);

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, $this->moduleNameLower);
        } else {
            $this->loadTranslationsFrom(module_path($this->moduleName, 'Resources/lang'), $this->moduleNameLower);
        } */
    }

    /**
     * Register an additional directory of factories.
     *
     * @return void
     */
    public function registerFactories()
    {
        /* if (! app()->environment('production') && $this->app->runningInConsole()) {
            app(Factory::class)->load(module_path($this->moduleName, 'Database/factories'));
        } */
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }

    private function getPublishableViewPaths() //: array
    {
        /* $paths = [];
        foreach (\Config::get('view.paths') as $path) {
            if (is_dir($path . '/modules/' . $this->moduleNameLower)) {
                $paths[] = $path . '/modules/' . $this->moduleNameLower;
            }
        }
        return $paths; */
    }
}
