<?php

/**
 * App Entry Point.
 */
require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';

$routes = require_once __DIR__ . '/../router/route.php';

echo $app->run($routes);

exit();