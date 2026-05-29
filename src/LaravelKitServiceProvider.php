<?php

namespace ITHilbert\LaravelKit;

use Illuminate\Support\Facades\Response;
use Illuminate\Support\ServiceProvider;
use ITHilbert\LaravelKit\Commands\AuditFeedbackCommand;
use ITHilbert\LaravelKit\Commands\AuditFetchCommand;
use ITHilbert\LaravelKit\Commands\AuditRunCommand;
use ITHilbert\LaravelKit\Commands\ChangelogShowCommand;
use ITHilbert\LaravelKit\Commands\LaravelKitBackupSeeders;
use ITHilbert\LaravelKit\Commands\LaravelKitCopyFiles;
use ITHilbert\LaravelKit\Commands\LaravelKitInstallAll;
use ITHilbert\LaravelKit\Commands\LaravelKitInstallCustomer;
use ITHilbert\LaravelKit\Commands\LaravelKitInstallDataTables;
use ITHilbert\LaravelKit\Commands\LaravelKitInstallDebugbar;
use ITHilbert\LaravelKit\Commands\LaravelKitInstalliSeed;
use ITHilbert\LaravelKit\Commands\LaravelKitInstallMix;
use ITHilbert\LaravelKit\Commands\LaravelKitInstallUserAuth;
use ITHilbert\LaravelKit\Commands\LaravelKitPaths;
use ITHilbert\LaravelKit\Commands\OpenFtp;
use ITHilbert\LaravelKit\Commands\OpenSsh;
use ITHilbert\LaravelKit\Mcp\Tools\AiExpertReviewTool;
use ITHilbert\LaravelKit\Mcp\Tools\BackupSeedersTool;
use ITHilbert\LaravelKit\Mcp\Tools\RemoteExecuteTool;
use ITHilbert\LaravelKit\Mcp\Tools\RemoteSyncTool;

// use Illuminate\View\Compilers\BladeCompiler;
// use ITHilbert\LaravelKit\Helpers\DataTableScript;

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

        $this->loadMigrationsFrom(__DIR__.'/Database/Migrations');
        $this->loadViewsFrom(__DIR__.'/Views', 'laravelkit');
        $this->publishAssets();
        $this->loadRoutesFrom(__DIR__.'/Routes/web.php');
        $this->registerResponseMacros();
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        /*  $this->app->register(RouteServiceProvider::class); */
        $this->mergeConfigFrom(__DIR__.'/Config/mcp_remote.php', 'mcp_remote');

        $this->registerBladeExtensions();
        $this->setAliase();

        $this->registerMcpTools();
        $this->registerAiDatabaseConnection();
    }

    protected function registerMcpTools()
    {
        // Wenn Laravel Boost installiert ist, hängen wir unser Tool dynamisch an
        $tools = config('boost.mcp.tools.include', []);
        $tools[] = BackupSeedersTool::class;
        $tools[] = RemoteExecuteTool::class;
        $tools[] = RemoteSyncTool::class;
        $tools[] = AiExpertReviewTool::class;
        config(['boost.mcp.tools.include' => $tools]);
    }

    protected function registerAiDatabaseConnection()
    {
        $config = $this->app['config']->get('database.connections.ai_sqlite');

        if (is_null($config)) {
            $this->app['config']->set('database.connections.ai_sqlite', [
                'driver' => 'sqlite',
                'database' => storage_path('devtools_ai.sqlite'),
                'prefix' => '',
                'foreign_key_constraints' => env('DB_FOREIGN_KEYS', true),
            ]);
        }
    }

    public function setAliase()
    {
        // $this->app->alias(\ITHilbert\LaravelKit\Helpers\DataTableScript::class, 'DataTableScript');
    }

    public function publishAssets()
    {
        // Config File
        $this->publishes([
            // Config files
            __DIR__.'/Config/laravelkit.php' => config_path('laravelkit.php'),
            __DIR__.'/Config/ai.php' => config_path('ai.php'),
            __DIR__.'/Config/datatablescript.php' => config_path('datatablescript.php'),
            __DIR__.'/Config/mcp_remote.php' => config_path('mcp_remote.php'),
        ], 'config');

        // ###################################
        // Publish Ressources
        // ###################################
        $this->publishes([
            // Lang Files
            __DIR__.'/Publish/Resources/lang/de/master.php' => resource_path('lang/de/master.php'),
            __DIR__.'/Publish/Resources/lang/de/pagination.php' => resource_path('lang/de/pagination.php'),
            __DIR__.'/Publish/Resources/lang/de/validation.php' => resource_path('lang/de/validation.php'),

            // Views
            __DIR__.'/Publish/Resources/views/include/formdelete.blade.php' => resource_path('views/include/formdelete.blade.php'),
            __DIR__.'/Publish/Resources/views/include/message.blade.php' => resource_path('views/include/message.blade.php'),
            __DIR__.'/Publish/Resources/views/include/breadcrumb.blade.php' => resource_path('views/include/breadcrumb.blade.php'),
            // __DIR__.'/Publish/Resources/views/layouts' => resource_path('views/layouts'),
            // __DIR__.'/Publish/Resources/views/vue.blade.php' => resource_path('views/vue.blade.php'),
            // __DIR__.'/Publish/Resources/views/vue-submit.blade.php' => resource_path('views/vue-submit.blade.php'),

            // Ressourcen nach Ressourcen
            __DIR__.'/Publish/Resources/css' => resource_path('css/vendor'),
            __DIR__.'/Publish/Resources/css/custom.css' => resource_path('css/custom.css'),
            __DIR__.'/Publish/Resources/images' => resource_path('images/vendor'),
            __DIR__.'/Publish/Resources/js' => resource_path('js/vendor'),
            __DIR__.'/Publish/Resources/js/vueapp.js' => resource_path('js/vueapp.js'),
            __DIR__.'/Publish/Resources/js/custom.js' => resource_path('js/custom.js'),
            __DIR__.'/Publish/Resources/js/AppLaravelKit.js' => resource_path('js/AppLaravelKit.js'),
            __DIR__.'/Publish/Resources/json' => resource_path('json/vendor'),
            __DIR__.'/Publish/Resources/scss' => resource_path('scss'),
            __DIR__.'/Publish/Resources/webfonts' => resource_path('webfonts/vendor'),
        ], 'resources');

        // Publish/Public nach Public
        $this->publishes([
            __DIR__.'/Publish/Public/css' => public_path('css'),
            __DIR__.'/Publish/Public/fonts' => public_path('fonts'),
            __DIR__.'/Publish/Public/images' => public_path('images'),
            __DIR__.'/Publish/Public/js' => public_path('js'),
            __DIR__.'/Publish/Public/webfonts' => public_path('webfonts'),
            __DIR__.'/Publish/Public/laravelkit' => public_path('vendor/laravelkit'),
        ], 'public');

    }

    protected function registerCommands()
    {
        $this->commands(LaravelKitBackupSeeders::class);
        $this->commands(LaravelKitPaths::class);
        $this->commands(LaravelKitCopyFiles::class);
        $this->commands(LaravelKitInstallAll::class);
        $this->commands(LaravelKitInstallCustomer::class);
        $this->commands(LaravelKitInstallDataTables::class);
        $this->commands(LaravelKitInstallDebugbar::class);
        $this->commands(LaravelKitInstalliSeed::class);
        // $this->commands( \ITHilbert\LaravelKit\Commands\LaravelKitInstallSite::class );
        $this->commands(LaravelKitInstallUserAuth::class);
        $this->commands(LaravelKitInstallMix::class);
        $this->commands(OpenFtp::class);
        $this->commands(OpenSsh::class);
        $this->commands(AuditRunCommand::class);
        $this->commands(AuditFetchCommand::class);
        $this->commands(AuditFeedbackCommand::class);
        $this->commands(ChangelogShowCommand::class);
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

    protected function registerResponseMacros()
    {
        Response::macro('apiSuccess', function ($data = null, $message = 'Success', $status = 200) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => $data,
            ], $status);
        });

        Response::macro('apiError', function ($message = 'Error', $status = 400, $errors = null) {
            return response()->json([
                'success' => false,
                'message' => $message,
                'errors' => $errors,
            ], $status);
        });

        Response::macro('apiNoContent', function () {
            return response()->noContent();
        });
    }
}
