<?php

namespace Ignite\Reports\Providers;

use Ignite\Reports\Library\PatientManagementFunctions;
use Ignite\Reports\Repositories\PatientRepository;
use Illuminate\Support\ServiceProvider;

class ReportsServiceProvider extends ServiceProvider {

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot() {
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->registerBindings();
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register() {
        //
    }

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig() {
        $this->publishes([
            __DIR__ . '/../Config/config.php' => config_path('reports.php'),
        ]);
        $this->mergeConfigFrom(
                __DIR__ . '/../Config/config.php', 'reports'
        );
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews() {
        $viewPath = base_path('resources/views/modules/reports');

        $sourcePath = __DIR__ . '/../Resources/views';

        $this->publishes([
            $sourcePath => $viewPath
        ]);

        $this->loadViewsFrom(array_merge(array_map(function ($path) {
                            return $path . '/modules/reports';
                        }, \Config::get('view.paths')), [$sourcePath]), 'reports');
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations() {
        $langPath = base_path('resources/lang/modules/reports');

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'reports');
        } else {
            $this->loadTranslationsFrom(__DIR__ . '/../Resources/lang', 'reports');
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides() {
        return [];
    }

    private function registerBindings() {
        $this->app->bind(PatientRepository::class, PatientManagementFunctions::class);
    }

}
