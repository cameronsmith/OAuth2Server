<?php

$injector = new Auryn\Injector;

$request = Slim\Http\Request::createFromGlobals($_SERVER);
$injector->share($request);

return $injector;