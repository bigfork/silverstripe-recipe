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
            'sql-host' => $io->ask('Please specify the database host: '),
            'sql-name' => $io->ask('Please specify the database name: '),
            'sentry-dsn' => $io->ask('Please enter the Sentry DSN (if applicable): '),
        ];

        self::applyConfiguration($config);
        self::installNpm();
        self::removeReadme();

        exit;
    }

    /**
     * Replaces placeholders in .env.example and renames it to .env
     *
     * @param array $config
     */
    protected static function applyConfiguration(array $config)
    {
        $base = self::getBasepath();

        $envPath = $base . '/.env';
        $templatePath = $base . '/.env.example';
        if (file_exists($templatePath) && !file_exists($envPath)) {
            $env = file_get_contents($templatePath);
            $env = str_replace(
                ['{sql-host}', '{sql-name}'],
                [$config['sql-host'], $config['sql-name']],
                $env
            );

            if (isset($config['sentry-dsn']) && $config['sentry-dsn']) {
                $env .= "\nSENTRY_DSN='{$config['sentry-dsn']}'\n";
            }

            file_put_contents($envPath, $env);
        }
    }

    /**
     * Runs "npm install" if a package.json file is present in the project.
     */
    protected static function installNpm()
    {
        $basePath = self::getBasepath();
        $themePath = $basePath . '/themes/default/';

        if (file_exists($themePath . '/package.json')) {
            $current = __DIR__;
            chdir($themePath);
            echo shell_exec('nvm use && npm install');
            chdir($current);
        }
    }

    /**
     * Removes README.md from the project.
     */
    protected static function removeReadme()
    {
        $basePath = self::getBasepath();

        if (file_exists($basePath . '/README.md')) {
            unlink($basePath . '/README.md');
        }
    }
}
