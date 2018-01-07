<?php

require __DIR__.'/vendor/autoload.php';

use Symfony\Component\Console\Application;
use App\Commands\SetupServerCommand;
use App\Commands\ServeCommand;

$application = new Application();

$application->add(new SetupServerCommand);
$application->add(new ServeCommand);

$application->run();