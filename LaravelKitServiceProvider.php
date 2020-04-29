<?php

namespace ITHilbert\LaravelKit\;

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

    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->registerFactories();
        //$this->loadMigrationsFrom(module_path($this->moduleName, 'Database/Migrations'));

        //$path = module_path($this->moduleName);

        //Dateien kopieren
        $this->publishes([
            //##################
            //# public
            //##################


            //Json
            //$path .'Publish/public/DataTable_DE.json' => public_path('css/DataTable_DE.json'),

/*             //CSS Files
            $path .'Publish/public/css/app.css' => resource_path('views/Lvkit/app.css'),
            $path .'Publish/public/css/custom.css' => public_path('css/custom.php'),
            $path .'Publish/public/css/datatables.css' => public_path('css/datatables.php'),
            $path .'Publish/public/css/datatables.min.css' => public_path('css/datatables.min.php'),
            $path .'Publish/public/css/jquery-ui.combobox.css' => public_path('css/jquery-ui.combobox.php'),
            $path .'Publish/public/css/jquery-ui.css' => public_path('css/jquery-ui.php'),
            $path .'Publish/public/css/jquery-ui.structure.css' => public_path('css/jquery-ui.structure.php'),
            $path .'Publish/public/css/jquery-ui.theme.css' => public_path('css/jquery-ui.theme.php'),
            $path .'Publish/public/css/LaravelKit.css' => public_path('css/LaravelKit.php'),

            //JS Files
            $path .'Publish/public/js/app.js' => public_path('css/app.js'),
            $path .'Publish/public/js/datatables.js' => public_path('css/datatables.js'),
            $path .'Publish/public/js/datatables.min.js' => public_path('css/datatables.min.js'),
            $path .'Publish/public/js/jquery-ui.combobox.js' => public_path('css/jquery-ui.combobox.js'),
            $path .'Publish/public/js/jquery-ui.js' => public_path('css/jquery-ui.js'),
            $path .'Publish/public/js/jquery-ui.min.js' => public_path('css/jquery-ui.min.js'),
            $path .'Publish/public/js/laravelKit.js' => public_path('css/laravelKit.js'), */


            //$path .'/Publish/public/css/app.css' => config_path('contact.php'),
            //$path .'/ressources/views/laravelKit' => resource_path('views/laravelKit'),
            //$path .'/ressources/lang/de' => resource_path('lang/de')
        ]);

    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(RouteServiceProvider::class);
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

    private function getPublishableViewPaths(): array
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
