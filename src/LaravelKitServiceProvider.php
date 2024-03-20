<?php

namespace ITHilbert\LaravelKit;

use Illuminate\Support\ServiceProvider;
use ITHilbert\LaravelKit\Components\Datenschutz;
use ITHilbert\LaravelKit\Components\AGB;
use ITHilbert\LaravelKit\Components\CookieRichtlinie;
use ITHilbert\LaravelKit\Components\Impressum;
//use Illuminate\View\Compilers\BladeCompiler;
//use ITHilbert\LaravelKit\Helpers\DataTableScript;

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
        $this->loadViewsFrom(__DIR__.'/Views', 'laravelkit');
        $this->publishAssets();
        //$this->loadRoutesFrom(__DIR__ . '/Routes/web.php');

        //Components
        $this->loadViewComponentsAs('laravelkit', [
            'agb' => AGB::class,
            'cookies' => CookieRichtlinie::class,
            'datenschutz' => Datenschutz::class,
            'impressum' => Impressum::class,
        ]);

    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
       /*  $this->app->register(RouteServiceProvider::class); */
       $this->registerBladeExtensions();
       $this->setAliase();
    }

    public function setAliase(){
        //$this->app->alias(\ITHilbert\LaravelKit\Helpers\DataTableScript::class, 'DataTableScript');
    }

    public function publishAssets()
    {
        //Config File
        $this->publishes([
            //Config files
            __DIR__ .'/Config/laravelkit.php' => config_path('laravelkit.php'),
            __DIR__ .'/Config/datatablescript.php' => config_path('datatablescript.php'),
        ], 'config');

        //###################################
        // Publish Ressources
        //###################################
        $this->publishes([
            //Lang Files
            __DIR__.'/Publish/Resources/lang/de/master.php' => resource_path('lang/de/master.php'),
            __DIR__.'/Publish/Resources/lang/de/pagination.php' => resource_path('lang/de/pagination.php'),
            __DIR__.'/Publish/Resources/lang/de/validation.php' => resource_path('lang/de/validation.php'),

            //Views
            __DIR__.'/Publish/Resources/views/include/formdelete.blade.php' => resource_path('views/include/formdelete.blade.php'),
            __DIR__.'/Publish/Resources/views/include/message.blade.php' => resource_path('views/include/message.blade.php'),
            __DIR__.'/Publish/Resources/views/include/breadcrumb.blade.php' => resource_path('views/include/breadcrumb.blade.php'),
            //__DIR__.'/Publish/Resources/views/layouts' => resource_path('views/layouts'),
            //__DIR__.'/Publish/Resources/views/vue.blade.php' => resource_path('views/vue.blade.php'),
            //__DIR__.'/Publish/Resources/views/vue-submit.blade.php' => resource_path('views/vue-submit.blade.php'),

            //Ressourcen nach Ressourcen
            __DIR__ .'/Publish/Resources/css' => resource_path('css/vendor'),
            __DIR__ .'/Publish/Resources/css/custom.css' => resource_path('css/custom.css'),
            __DIR__ .'/Publish/Resources/images' => resource_path('images/vendor'),
            __DIR__ .'/Publish/Resources/js' => resource_path('js/vendor'),
            __DIR__ .'/Publish/Resources/js/vueapp.js' => resource_path('js/vueapp.js'),
            __DIR__ .'/Publish/Resources/js/custom.js' => resource_path('js/custom.js'),
            __DIR__ .'/Publish/Resources/js/AppLaravelKit.js' => resource_path('js/AppLaravelKit.js'),
            __DIR__ .'/Publish/Resources/json' => resource_path('json/vendor'),
            __DIR__ .'/Publish/Resources/scss' => resource_path('scss'),
            __DIR__ .'/Publish/Resources/webfonts' => resource_path('webfonts/vendor'),
        ], 'resources');

        //Publish/Public nach Public
        $this->publishes([
            __DIR__ .'/Publish/Public/css' => public_path('css'),
            __DIR__ .'/Publish/Public/fonts' => public_path('fonts'),
            __DIR__ .'/Publish/Public/images' => public_path('images'),
            __DIR__ .'/Publish/Public/js' => public_path('js'),
            __DIR__ .'/Publish/Public/webfonts' => public_path('webfonts'),
            __DIR__ .'/Publish/Public/laravelkit' => public_path('vendor/laravelkit'),
       ], 'public');

    }


    protected function registerCommands()
    {
        $this->commands( \ITHilbert\LaravelKit\Commands\LaravelKitPaths::class );
        $this->commands( \ITHilbert\LaravelKit\Commands\LaravelKitCopyFiles::class );
        //$this->commands( \ITHilbert\LaravelKit\Commands\LaravelKitInstallAdminLte::class );
        $this->commands( \ITHilbert\LaravelKit\Commands\LaravelKitInstallAll::class );
        $this->commands( \ITHilbert\LaravelKit\Commands\LaravelKitInstallCustomer::class );
        $this->commands( \ITHilbert\LaravelKit\Commands\LaravelKitInstallDataTables::class );
        $this->commands( \ITHilbert\LaravelKit\Commands\LaravelKitInstallDebugbar::class );
        $this->commands( \ITHilbert\LaravelKit\Commands\LaravelKitInstalliSeed::class );
        $this->commands( \ITHilbert\LaravelKit\Commands\LaravelKitInstallSite::class );
        $this->commands( \ITHilbert\LaravelKit\Commands\LaravelKitInstallUserAuth::class );
        $this->commands( \ITHilbert\LaravelKit\Commands\LaravelKitInstallMix::class );
    }

    protected function registerBladeExtensions()
    {
        /* $this->app->afterResolving('blade.compiler', function (BladeCompiler $bladeCompiler) {

            // dataTable
            $bladeCompiler->directive('dataTable', function ($role, $guard= '') {
                return "<?php \$dt = new " . DataTableScript::class . "(); ?>";
            });
            // enddataTable
            $bladeCompiler->directive('enddataTable', function ($role, $guard= '') {
                return " ?>";
            });

        }); */
    }



}
