<?php

$router = FastRoute\simpleDispatcher(function (FastRoute\RouteCollector $r) {
    /* Password Grant */
    $r->addRoute('POST', '/password', ['App\Controllers\PasswordController', 'authorize']);

    /* Authorization Grant */
    $r->addRoute('GET', '/auth-code', ['App\Controllers\AuthCodeController', 'authorize']);
    $r->addRoute('POST', '/auth-code', ['App\Controllers\AuthCodeController', 'provideToken']);

    /* Refresh Grant */
    $r->addRoute('POST', '/refresh', ['App\Controllers\RefreshController', 'authorize']);
});

return $router;