<?php namespace App\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use App\Helpers\Path;
use Dotenv\Dotenv;
use Exception;
use Phinx\Console\PhinxApplication;
use Phinx\Wrapper\TextWrapper;

class SetupServerCommand extends Command
{
    const ENCRYPT_BITS = 2048;

    /**
     * Configure application.
     */
    protected function configure()
    {
        $this->setName('app:setup')
            ->setDescription('Creates a new setup of the oauth2 server.')
            ->setHelp('This command allows you to setup an oauth2 server and remove any existing installation.')
            ->addArgument(
                'reinstall',
                InputArgument::OPTIONAL,
                'Reinstall the application.'
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
        if (!$this->loadEnvFile()) {
            $output->writeln('<error>You must have a .env file in order to setup the server.</error>');
            $output->writeln('Please read the README.md file first.');
            return false;
        }

        $reinstall = $input->getArgument('reinstall');

        if ($reinstall && $this->alreadyAnExistingInstallation()) {
            $this->removeAppFiles();
        }

        if ($this->alreadyAnExistingInstallation()) {
            $output->writeln('<error>An existing installation already exists!</error>');
            $output->writeln('<info>Run the command again with reinstall. If you wish to reinstall the app.</info>');

            return false;
        }

        $output->write('Generate private key: ');
        $privateKeyResource = $this->generatePrivateKey();
        $output->writeln('<info>OK</info>');

        $output->write('Generate public key: ');
        $this->generatePublicKey($privateKeyResource);
        $output->writeln('<info>OK</info>');

        $output->write('Generate .env encryption key: ');
        $this->generateEncryptionKey(base64_encode(random_bytes(32)));
        $output->writeln('<info>OK</info>');

        $output->write('Create database: ');
        $this->createDatabase();
        $output->writeln('<info>OK</info>');

        $output->write('Run migrations: ');
        $this->runMigrations();
        $output->writeln('<info>OK</info>');

        return true;
    }

    /**
     * Attempt to load the env file.
     *
     * @return bool
     */
    protected function loadEnvFile()
    {
        try {
            (new Dotenv(__DIR__ . '/../../'))->load();
        } catch (Exception $exception) {
            return false;
        }

        return true;
    }

    /**
     * Is there already an existing installation.
     *
     * @return bool
     */
    protected function alreadyAnExistingInstallation()
    {
        return file_exists(Path::getStoragePath() . '/private.key');
    }


    /**
     * Remove existing application files.
     */
    protected function removeAppFiles()
    {
        $storagePath = Path::getStoragePath();
        $privateKey = $storagePath . '/private.key';
        $publicKey = $storagePath . '/public.key';
        $databaseFile = $storagePath . '/database.sqlite';

        if (file_exists($privateKey)) {
            unlink($privateKey);
        }

        if (file_exists($publicKey)) {
            unlink($publicKey);
        }

        if (file_exists($databaseFile)) {
            unlink($databaseFile);
        }
    }

    /**
     * Generate a private key and return it's resource.
     *
     * @return resource
     */
    protected function generatePrivateKey()
    {
        $privateKeyFile = Path::getStoragePath() . '/private.key';

        $resourceKey = openssl_pkey_new([
            'private_key_bits' => self::ENCRYPT_BITS,
        ]);

        openssl_pkey_export($resourceKey, $privateKeyContents);
        file_put_contents($privateKeyFile, $privateKeyContents);
        chmod($privateKeyFile, 0660);

        return $resourceKey;
    }

    /**
     * Generate public key from private key
     */
    protected function generatePublicKey($privateKeyResource)
    {
        $publicKeyFile = Path::getStoragePath() . '/public.key';

        $publicKey = openssl_pkey_get_details($privateKeyResource);
        $publicKeyContents = $publicKey['key'];
        file_put_contents($publicKeyFile, $publicKeyContents);
        chmod($publicKeyFile, 0660);
    }

    /**
     * Generate new encryption key for .env file.
     *
     * @param $newKey
     */
    protected function generateEncryptionKey($newKey)
    {
        $envFile = Path::getAppPath() . '/.env';

        file_put_contents($envFile, str_replace(
            'ENCRYPTION_KEY=' . getenv('ENCRYPTION_KEY'), 'ENCRYPTION_KEY=' . $newKey, file_get_contents($envFile)
        ));
    }

    /**
     * Create database.
     */
    protected function createDatabase()
    {
        touch(Path::getStoragePath() . '/database.sqlite');
    }

    /**
     * Run migrations.
     */
    protected function runMigrations()
    {
        $phinxApp = new PhinxApplication;
        $phinxTextWrapper = new TextWrapper($phinxApp);

        $phinxTextWrapper->setOption('configuration', Path::getAppPath() . '/phinx.yml');
        $phinxTextWrapper->setOption('parser', 'YAML');
        $phinxTextWrapper->setOption('environment', 'development');

        $phinxTextWrapper->getMigrate();
    }
}