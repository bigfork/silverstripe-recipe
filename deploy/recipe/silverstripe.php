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
    $dbName = ask('Please enter the database name', get('application'));
    $sentryDSN = ask('Please enter the Sentry DSN (if applicable)');
    $stage = Context::get()->getHost()->getConfig()->get('stage');
    $dbPrefix = in_array($stage, ['test', 'stage', 'staging']) ? '_stage_' : '';
    $type = in_array($stage, ['test', 'stage', 'staging']) ? 'test' : 'live';

    $contents = <<<ENV
SS_DATABASE_CLASS='MySQLPDODatabase'
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
    $httpUser = get('http_user', false);
    if ($httpUser === false) {
        // Detect http user in process list.
        $httpUser = run("ps axo user,comm | grep -E '[a]pache|[h]ttpd|[_]www|[w]ww-data|[n]ginx' | grep -v root | head -1 | cut -d ' ' -f1");
        if (empty($httpUser)) {
            throw new RuntimeException(
                "Can't detect http user name.\n" .
                "Please setup `http_user` config parameter."
            );
        }
    }

    run("sudo -u {$httpUser} {{release_path}}/vendor/bin/sake dev/build flush");
});

desc('Create directory for sspak dumps');
task('silverstripe:create_dump_dir', function () {
    run("cd {{deploy_path}} && if [ ! -d dumps ]; then mkdir dumps; fi");
})->setPrivate();

desc('Upload assets');
task('silverstripe:upload_assets', function () {
    $filename = get('application') . '-assets-' . date('Y-m-d-H-i-s') . '.sspak';
    $local = sys_get_temp_dir() . '/' . $filename;

    // Dump assets from local copy and upload
    runLocally("sspak save --assets . $local", [
        'timeout' => 1800
    ]);
    upload($local, "{{deploy_path}}/dumps/");

    // Deploy assets
    run("cd {{release_path}} && sspak load --assets {{deploy_path}}/dumps/{$filename}", [
        'timeout' => 1800
    ]);

    // Tidy up
    runLocally("rm $local");
    run("rm {{deploy_path}}/dumps/{$filename}");
});
before('silverstripe:upload_assets', 'silverstripe:create_dump_dir');
after('silverstripe:upload_assets', 'deploy:writable');

desc('Upload database');
task('silverstripe:upload_database', function () {
    $filename = get('application') . '-db-' . date('Y-m-d-H-i-s') . '.sspak';
    $local = sys_get_temp_dir() . '/' . $filename;

    // Dump database from local copy and upload
    runLocally("sspak save --db . $local");
    upload($local, "{{deploy_path}}/dumps/");

    // Deploy database
    run("cd {{release_path}} && sspak load --db {{deploy_path}}/dumps/{$filename}");

    // Tidy up
    runLocally("rm $local");
    run("rm {{deploy_path}}/dumps/{$filename}");
});
before('silverstripe:upload_database', 'silverstripe:create_dump_dir');

desc('Download assets');
task('silverstripe:download_assets', function () {
    $filename = get('application') . '-assets-' . date('Y-m-d-H-i-s') . '.sspak';
    $local = sys_get_temp_dir() . '/' . $filename;

    // Dump assets from remote copy and download
    run("cd {{release_path}} && sspak save --assets . {{deploy_path}}/dumps/{$filename}", [
        'timeout' => 1800
    ]);
    download("{{deploy_path}}/dumps/{$filename}", $local);

    // Import assets
    runLocally("sspak load --assets {$local}", [
        'timeout' => 1800
    ]);

    // Tidy up
    runLocally("rm $local");
    run("rm {{deploy_path}}/dumps/{$filename}");
});
before('silverstripe:download_assets', 'silverstripe:create_dump_dir');

desc('Download database');
task('silverstripe:download_database', function () {
    $filename = get('application') . '-db-' . date('Y-m-d-H-i-s') . '.sspak';
    $local = sys_get_temp_dir() . '/' . $filename;

    // Dump database from remote copy and download
    run("cd {{release_path}} && sspak save --db . {{deploy_path}}/dumps/{$filename}");
    download("{{deploy_path}}/dumps/{$filename}", $local);

    // Import database
    runLocally("sspak load --db {$local}");

    // Tidy up
    runLocally("rm $local");
    run("rm {{deploy_path}}/dumps/{$filename}");
});
before('silverstripe:download_database', 'silverstripe:create_dump_dir');
