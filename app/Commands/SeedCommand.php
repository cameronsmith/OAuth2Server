<?php namespace App\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\Helpers\Path;
use Phinx\Console\PhinxApplication;
use Phinx\Wrapper\TextWrapper;

class SeedCommand extends Command
{
    /**
     * Configure application.
     */
    protected function configure()
    {
        $this->setName('app:seed')
            ->setDescription('Seed test data for the oauth2 server.')
            ->setHelp('This command allows you seed test data to the oauth2 server.');
    }

    /**
     * Execute application.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return bool
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (getenv('APP_ENV') !== 'development') {
            $output->writeln('<error>Seeders can ONLY be ran during development</error>');
            return false;
        }

        $output->write('Seeding data: ');

        $phinxApp = new PhinxApplication;
        $phinxTextWrapper = new TextWrapper($phinxApp);

        $phinxTextWrapper->setOption('configuration', Path::getAppPath() . '/phinx.yml');
        $phinxTextWrapper->setOption('parser', 'YAML');
        $phinxTextWrapper->setOption('environment', 'development');

        $phinxTextWrapper->getSeed();

        $output->writeln('<info>OK</info>');

        return true;
    }
}