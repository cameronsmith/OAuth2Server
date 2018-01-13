<?php

/**
 * Load autoloader.
 */
require_once __DIR__ . '/../vendor/autoload.php';

/**
 * Init application
 */
$app = require_once __DIR__ . '/../bootstrap/app.php';

/**
 * Register routes
 */
$routes = require_once __DIR__ . '/../router/route.php';

$app->addRoutes($routes);
echo $app->run();

exit();