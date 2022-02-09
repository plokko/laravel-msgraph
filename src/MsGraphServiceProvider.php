<?php
namespace plokko\MsGraph;

use Illuminate\Support\ServiceProvider;

class MsGraphServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //$this->loadViewsFrom(__DIR__.'/../resources/views', 'table-helper');
        $this->publishes([
            //__DIR__.'/../resources/views' => resource_path('views/vendor/ms-graph'),
            __DIR__.'/../config/config.php' => config_path('ms-graph.php'),
        ]);

        /*//--- Console commands ---///
        if ($this->app->runningInConsole())
        {
            $this->commands([
                GenerateCommand::class,
            ]);
        }
        */
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // Merge default config ///
        $this->mergeConfigFrom(
            __DIR__.'/../config/config.php', 'ms-graph'
        );
        // Facade accessor
        $this->app->bind(MsGraph::class, function($app) {
            return new MsGraph();
        });

        /*
        ///Blade directive
        Blade::directive('locales', function ($locale=null) {
            $lm = \App::make(LocaleManager::class);
            $urls = $lm->listLocaleUrls();
            return '<script src="<?php echo optional('.(var_export($urls,true)).')['.($locale?var_export($locale,true):'App::getLocale()').']; ?>" ></script>';
        });
        */
    }

    public function provides()
    {
        return [
            MsGraph::class,
        ];
    }
}
