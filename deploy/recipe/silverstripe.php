<?php

namespace Deployer;

use Deployer\Task\Context;
use RuntimeException;

// Tasks
desc('Populate .env file');
task('silverstripe:create_dotenv', function () {
    $envPath = "{{deploy_path}}/shared/.env";
    if (test("[ -f {$envPath} ]")) {
        return;
    }

    $dbServer = ask('Please enter the database server', 'localhost');
    $dbUser = ask('Please enter the database username');
    $dbPass = str_replace("'", "\\'", askHiddenResponse('Please enter the database password'));
    $dbName = ask('Please enter the database name');
    $sentryDSN = ask('Please enter the Sentry DSN (if applicable)');
    $stage = Context::get()->getHost()->getConfig()->get('stage');
    $dbPrefix = $stage === 'production' ? '' : '_stage_';
    $type = $stage === 'production' ? 'live' : 'test';

    $contents = <<<ENV
SS_DATABASE_CLASS='MySQLDatabase'
SS_DATABASE_USERNAME='{$dbUser}'
SS_DATABASE_PASSWORD='{$dbPass}'
SS_DATABASE_SERVER='{$dbServer}'
SS_DATABASE_NAME='{$dbName}'
SS_DATABASE_PREFIX='{$dbPrefix}'
SS_ENVIRONMENT_TYPE='{$type}'
ENV;

    if ($sentryDSN) {
        $contents .= "\nSENTRY_DSN='{$sentryDSN}'";
    }

    $command = <<<BASH
cat >{$envPath} <<EOL
$contents
EOL
BASH;

    run("$command");
})->setPrivate();

desc('Run composer vendor-expose');
task('silverstripe:vendor_expose', function () {
    run('cd {{release_path}} && {{bin/composer}} vendor-expose');
});

desc('Create silverstripe-cache directory');
task('silverstripe:create_cache_dir', function () {
    run("cd {{release_path}} && if [ ! -d silverstripe-cache ]; then mkdir silverstripe-cache; fi");
})->setPrivate();

desc('Run dev/build');
task('silverstripe:dev_build', function () {
    run("{{release_path}}/vendor/bin/sake dev/build flush");
});

desc('Create directory for sspak dumps');
task('silverstripe:create_dump_dir', function () {
    run("cd {{deploy_path}} && if [ ! -d dumps ]; then mkdir dumps; fi");
})->setPrivate();

desc('Upload assets');
task('silverstripe:upload_assets', function () {
    upload('public/assets/', '{{deploy_path}}/shared/public/assets', [
        'options' => [
            "--exclude={'error-*.html','.htaccess','.DS_Store'}",
            "--delete"
        ]
    ]);
});
after('silverstripe:upload_assets', 'deploy:writable');

desc('Upload database');
task('silverstripe:upload_database', function () {
    writeln("<error>This task is currently disabled.</error>");
    exit;
});
before('silverstripe:upload_database', 'silverstripe:create_dump_dir');

desc('Download assets');
task('silverstripe:download_assets', function () {
    download('{{deploy_path}}/shared/public/assets/', 'public/assets', [
        'options' => [
            "--exclude={'error-*.html','.htaccess'}",
            "--delete"
        ]
    ]);
});

desc('Download database');
task('silverstripe:download_database', function () {
    writeln("<error>This task is currently disabled.</error>");
    exit;
});
before('silverstripe:download_database', 'silverstripe:create_dump_dir');
