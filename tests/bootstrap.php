<?php

/**
 * Load autoloader.
 */
require_once __DIR__ . '/../vendor/autoload.php';

/**
 * Load environment file.
 */
(new Dotenv\Dotenv(__DIR__ . '/../'))->load();

