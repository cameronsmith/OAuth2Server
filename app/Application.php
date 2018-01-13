<?php namespace App;

use FastRoute\Dispatcher;

class Application
{
    protected $injector;

    /**
     * Application constructor.
     *
     * @param $injector
     */
    public function __construct($injector)
    {
        $this->injector = $injector;
    }

    /**
     * Run through application routes.
     *
     * @param $routes
     */
    public function run($routes) {
        $request = $this->injector->make('Slim\Http\Request');
        $routeInfo = $routes->dispatch($request->getMethod(), $request->gerUri());

        switch ($routeInfo[0]) {
            case Dispatcher::FOUND:
                $class = $routeInfo[1][0];
                $method = $routeInfo[1][1];

                $controller = $this->injector->make($class);
                return $controller->$method();
                break;
            default:
                http_response_code(404);
                return;
                break;
        }
    }

    /**
     * Bind a singleton or overwrite singleton.
     *
     * @param $instance
     */
    public function bindSingleton($instance) {
        $this->injector->share($instance);
    }
}