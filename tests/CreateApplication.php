<?php namespace Tests;

use App\Application;
use App\Helpers\Path;
use Phinx\Console\PhinxApplication;
use Phinx\Wrapper\TextWrapper;

trait CreateApplication
{
    /**
     * Return an application instance
     *
     * @return Application
     */
    public function getAppInstance() {
        $app = require Path::getAppPath() . '/bootstrap/app.php';
        $routes = require Path::getAppPath() . '/router/route.php';
        $app->addRoutes($routes);

        return $app;
    }

    /**
     * Clear Database + Run migrations.
     */
    public function runMigrations()
    {
        $this->clearDatabase();
        $phinxApp = new PhinxApplication;
        $phinxTextWrapper = new TextWrapper($phinxApp);

        $phinxTextWrapper->setOption('configuration', Path::getAppPath() . '/phinx.yml');
        $phinxTextWrapper->setOption('parser', 'YAML');
        $phinxTextWrapper->setOption('environment', 'testing');

        $phinxTextWrapper->getMigrate();
    }

    /**
     * Clear Database.
     *
     * @return bool
     */
    protected function clearDatabase() {
        $databaseFile = Path::getStoragePath() . DIRECTORY_SEPARATOR . getenv('DB_FILE');

        if (file_exists($databaseFile)) {
            return unlink(Path::getStoragePath() . DIRECTORY_SEPARATOR . getenv('DB_FILE'));
        }

        return true;
    }

}