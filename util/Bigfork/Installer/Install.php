<?php
namespace Bigfork\Installer;

use Composer\Script\Event;
require 'vendor/autoload.php';

/**
 * Post create-project helper
 */
class Install
{
    /**
     * @var string
     */
    private static $basePath = '';

    /**
     * Get the document root for this project. Attempts getcwd(), falls back to
     * directory traversal.
     *
     * @return string
     */
    public static function getBasepath()
    {
        if (!self::$basePath) {
            $candidate = getcwd() ?: dirname(dirname(dirname(dirname(__FILE__))));
            self::$basePath = rtrim($candidate, DIRECTORY_SEPARATOR);
        }

        return self::$basePath;
    }

    /**
     * Called after every "composer update" command, or after a "composer install"
     * command has been executed without a lock file present
     *
     * @param Composer\Script\Event $event
     */
    public static function postCreateProject(Event $event)
    {
        $io = $event->getIO();
        $config = [
            'ddevShouldStart' => $io->askConfirmation('Would you like DDEV to start after the installation is complete?: [y/n] ', true),
        ];

        self::updateDDevName();
        self::copyEnv();
        self::removeReadme();
        self::installNpm();

        if ($config['ddevShouldStart']) {
            self::startDDEV();
        }

        exit;
    }

    /**
     * Update the sitename placeholder in .ddev/config.yaml with the directory name
     */
    protected static function updateDDevName(): void
    {
        $base = self::getBasepath();
        $directories = explode('/', $base);
        $directoryName = array_pop($directories);
        $ddevConfigPath = $base . '/.ddev/config.yaml';

        if (file_exists($ddevConfigPath)) {
            $ddevConfig = file_get_contents($ddevConfigPath);
            $ddevConfig = str_replace('{sitename}', $directoryName, $ddevConfig);

            file_put_contents($ddevConfigPath, $ddevConfig);
        }
    }

    /**
     * Copies the .env.example to .env if it doesn't exist
     */
    protected static function copyEnv(): void
    {
        $base = self::getBasepath();

        $envPath = $base . '/.env';
        $examplePath = $base . '/.env.example';
        if (file_exists($examplePath) && !file_exists($envPath)) {
            copy($examplePath, $envPath);
        }
    }

    /**
     * Runs "npm install" if a package.json file is present in the project.
     */
    protected static function installNpm(): void
    {
        $basePath = self::getBasepath();
        $themePath = $basePath . '/themes/default/';

        if (file_exists($themePath . '/package.json')) {
            echo shell_exec('ddev theme install');
        }
    }

    /**
     * Removes README.md from the project.
     */
    protected static function removeReadme(): void
    {
        $basePath = self::getBasepath();

        if (file_exists($basePath . '/README.md')) {
            unlink($basePath . '/README.md');
        }
    }

    protected static function startDDEV(): void
    {
        echo shell_exec('ddev start');
    }
}
