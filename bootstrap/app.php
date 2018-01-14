<?php

/**
 * Load environment file.
 */
(new Dotenv\Dotenv(__DIR__ . '/../'))->load();

/**
 * Boot application
 */
$app = new App\Application(new Auryn\Injector);

/**
 * Bind singltons
 */
$app->bindSingleton(Slim\Http\Request::createFromGlobals($_SERVER));

$app->bindSingleton(App\Repositories\RepositoryConnection::getConnectionInstance([
    'file' => App\Helpers\Path::getStoragePath() . DIRECTORY_SEPARATOR . getenv('DB_FILE')
]));

return $app;