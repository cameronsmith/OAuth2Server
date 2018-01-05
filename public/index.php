<?php

/**
 * App Entry Point.
 *
 * - Setup autoloading
 * - Get environment files
 * - Pass execution onto routes.
 */
require_once __DIR__ . '/../vendor/autoload.php';

(new Dotenv\Dotenv(__DIR__ . '/../'))->load();

require_once __DIR__ . '/../router/route.php';