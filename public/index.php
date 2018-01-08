<?php

/**
 * App Entry Point.
 */
require_once __DIR__ . '/../vendor/autoload.php';

(new Dotenv\Dotenv(__DIR__ . '/../'))->load();

require_once __DIR__ . '/../bootstrap/injector.php';

require_once __DIR__ . '/../router/route.php';

exit();