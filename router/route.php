<?php

$router = FastRoute\simpleDispatcher(function (FastRoute\RouteCollector $r) {
    $r->addRoute('POST', '/password', ['App\Controllers\PasswordController', 'authorize']);
});

return $router;