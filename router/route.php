<?php

$dispatcher = FastRoute\simpleDispatcher(function (FastRoute\RouteCollector $r) {
    $r->addRoute('POST', '/password', function () {
        return (new \App\Controllers\PasswordController)->authorize();
    });
});

$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);

switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];
        echo $handler();
        break;
    default:
        http_response_code(404);
        die();
        break;
}
