<?php namespace Tests;

use App\Application;
use App\Helpers\Path;

trait CreateApplication
{
    /**
     * Return an application instance
     *
     * @return Application
     */
    public function getAppInstance() {
        $app = require_once Path::getAppPath() . '/bootstrap/app.php';
        $routes = require_once Path::getAppPath() . '/router/route.php';
        $app->addRoutes($routes);

        return $app;
    }

}