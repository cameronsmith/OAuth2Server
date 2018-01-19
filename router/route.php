<?php

$router = FastRoute\simpleDispatcher(function (FastRoute\RouteCollector $r) {
    $r->addRoute('POST', '/password', ['App\Controllers\PasswordController', 'authorize']);
    $r->addRoute('POST', '/refresh', ['App\Controllers\RefreshController', 'authorize']);
});

return $router;