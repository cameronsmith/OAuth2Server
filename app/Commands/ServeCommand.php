<?php namespace App\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;

class ServeCommand extends Command
{
    /**
     * Configure application.
     */
    protected function configure()
    {
        $this->setName('app:serve')
            ->setDescription('Starts a new oauth2 server.')
            ->setHelp('This command allows you to start a server.')
            ->addOption(
                'port',
                'p',
                InputOption::VALUE_REQUIRED,
                'What port would you like the server to listen on?',
                8080
            );
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
        $port = $input->getOption('port');

        $output->writeln('<info>Started new server on port: ' . $port . '.</info>');
        $output->writeln('Press ctrl+c to cancel');
        shell_exec('php -S localhost:' . $port . ' ./public/index.php');
        return true;
    }
}