<?php namespace App;

use FastRoute\Dispatcher;
use App\Providers;

class Application
{
    protected $injector;
    protected $routes;

    /**
     * Application constructor.
     *
     * @param $injector
     */
    public function __construct($injector)
    {
        $this->injector = $injector;
        $this->registerProviders(Providers::APP);
    }

    /**
     * addRoutes to application.
     *
     * @param $routes
     */
    public function addRoutes($routes) {
        $this->routes = $routes;
    }

    /**
     * Run through application routes.
     */
    public function run() {
        $request = $this->injector->make('Psr\Http\Message\ServerRequestInterface');
        $routeInfo = $this->routes->dispatch($request->getMethod(), $request->getUri()->getPath());

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
     * Register an interface with a class.
     *
     * @param $interface
     * @param $class
     */
    public function addInterfaceAlias($interface, $class) {
        $this->injector->alias($interface, $class);
    }

    /**
     * Bind a singleton or overwrite singleton.
     *
     * @param $instance
     */
    public function bindSingleton($instance) {
        $this->injector->share($instance);
    }

    /**
     * Register application providers with the injector.
     *
     * @param array $providers
     */
    protected function registerProviders(array $providers) {
        foreach($providers as $interface => $class) {
            $this->addInterfaceAlias($interface, $class);
        }
    }

}