<?php

namespace Ignite\Reports\Providers;

use Ignite\Core\Providers\CoreRouteServiceProvider;

class RouteServiceProvider extends CoreRouteServiceProvider {

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * @var string
     */
    protected $prefix = 'reports';

    /**
     * @var string
     */
    protected $namespace = 'Ignite\Reports\Http\Controllers';

    /**
     * @var string
     */
    protected $alias = 'reports';

    /**
     * @return string
     */
    protected function getModuleRoutes() {
        return __DIR__ . '/../Http/routes.php';
    }

    /**
     * @return string
     */
    protected function getApiRoutes() {
        return __DIR__ . '/../Http/apiRoutes.php';
    }

    protected function getFrontendRoutes() {
        return null;
    }

}
