<?php

require __DIR__.'/vendor/autoload.php';
require __DIR__.'/bootstrap/app.php';

use Symfony\Component\Console\Application;
use App\Commands\SetupServerCommand;
use App\Commands\ServeCommand;
use App\Commands\SeedCommand;

$application = new Application();

$application->add(new SetupServerCommand);
$application->add(new ServeCommand);
$application->add(new SeedCommand);

$application->run();