<?php

use App\Repositories\RepositoryConnection;

$injector = new Auryn\Injector;

$request = Slim\Http\Request::createFromGlobals($_SERVER);
$injector->share($request);

$pdo = new RepositoryConnection(
    getenv('DB_ADAPTER'). ':'. App\Helpers\Path::getStoragePath() . getenv('DB_FILE')
);

$pdo->setAttribute(RepositoryConnection::ATTR_ERRMODE, RepositoryConnection::ERRMODE_EXCEPTION);
$injector->share($pdo);

return $injector;