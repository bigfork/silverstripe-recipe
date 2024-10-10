<?php

namespace Deployer;

use Deployer\Task\Context;

// Tasks
desc('Populate .env file');
task('silverstripe:create_dotenv', function () {
    $envPath = "{{deploy_path}}/shared/.env";
    if (test("[ -f {$envPath} ]")) {
        return;
    }

    $dbServer = ask('Please enter the database server', 'localhost');
    $dbName = ask('Please enter the database name');
    $dbUser = ask('Please enter the database username', !empty($dbName) ? $dbName : null);
    $dbPass = str_replace("'", "\\'", ask('Please enter the database password'));
    $sentryDSN = ask('Please enter the Sentry DSN (if applicable)');
    $domain = ask('Please enter the full domain name including protocol, please exclude trailing /');
    $stage = Context::get()->getHost()->getConfig()->get('stage');
    $type = $stage === 'production' ? 'production' : 'staging';

    $contents = <<<ENV
DB_NAME='{$dbName}'
DB_USER='{$dbUser}'
DB_PASSWORD='{$dbPass}'
DB_HOST='{$dbServer}'
DB_PREFIX='wp_'
WP_ENV='{$stage}'
WP_HOME="{$domain}"
WP_SITEURL="{$domain}/wp"

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

desc('Upload assets');
task('silverstripe:upload_assets', function () {
    $stage = Context::get()->getHost()->getConfig()->get('stage') === 'production' ? 'live' : 'staging';
    if (!askConfirmation("Are you sure you want to overwrite the {$stage} assets?")) {
        echo "🐔\n";
        exit;
    }

    upload('public/assets/', '{{deploy_path}}/shared/public/assets', [
        'options' => [
            "--exclude={'error-*.html','_tinymce','.htaccess','.DS_Store','._*'}",
            "--delete"
        ]
    ]);
});
after('silverstripe:upload_assets', 'deploy:writable');

desc('Upload database');
task('silverstripe:upload_database', function () {
    $stage = Context::get()->getHost()->getConfig()->get('stage') === 'production' ? 'live' : 'staging';
    if (!askConfirmation("Are you sure you want to overwrite the {$stage} database?")) {
        echo "🐔\n";
        exit;
    }

    invoke('silverstripe:create_dump_dir');

    if (!testLocally('[ -f .env ]')) {
        writeln("<error>Unable to find .env file in local environment.</error>");
        exit;
    } elseif (!test('[ -f {{deploy_path}}/shared/.env ]')) {
        writeln("<error>Unable to find .env file on remote server.</error>");
        exit;
    }

    $filename = 'db-' . date('Y-m-d-H-i-s') . '.gz';
    $localPath = sys_get_temp_dir() . '/' . $filename;

    // Export database
    runLocally("ddev export-db > {$localPath}");

    // Upload database
    upload($localPath, "{{deploy_path}}/dumps/");

    // Import database
    run(getImportDatabaseCommand('{{deploy_path}}/shared/.env', "{{deploy_path}}/dumps/{$filename}"));

    // Tidy up
    runLocally("rm {$localPath}");
    run("rm {{deploy_path}}/dumps/{$filename}");
});

desc('Download assets');
task('silverstripe:download_assets', function () {
    download('{{deploy_path}}/shared/public/assets/', 'public/assets', [
        'options' => [
            "--exclude={'error-*.html','_tinymce','.htaccess','.DS_Store','._*'}",
            "--delete"
        ]
    ]);
});

desc('Download database');
task('silverstripe:download_database', function () {
    invoke('silverstripe:create_dump_dir');

    if (!testLocally('[ -f .env ]')) {
        writeln("<error>Unable to find .env file in local environment.</error>");
        exit;
    } elseif (!test('[ -f {{deploy_path}}/shared/.env ]')) {
        writeln("<error>Unable to find .env file on remote server.</error>");
        exit;
    }

    $filename = 'db-' . date('Y-m-d-H-i-s') . '.sql.gz';
    $localPath = sys_get_temp_dir() . '/' . $filename;

    // Export database
    run(getExportDatabaseCommand('{{deploy_path}}/shared/.env', "{{deploy_path}}/dumps/{$filename}"));

    // Download database
    download("{{deploy_path}}/dumps/{$filename}", $localPath);

    // Import database
    runLocally("ddev import-db --file {$localPath}");

    // Tidy up
    runLocally("rm {$localPath}");
    run("rm {{deploy_path}}/dumps/{$filename}");
});

set('mysql_default_charset', 'utf8');
set(
    'mysqldump_args',
    implode(' ', [
        '--no-tablespaces',
        '--skip-opt',
        '--add-drop-table',
        '--extended-insert',
        '--create-options',
        '--quick',
        '--set-charset',
        '--default-character-set={{mysql_default_charset}}'
    ])
);

set(
    'mysql_args',
    implode(' ', [
        '--default-character-set={{mysql_default_charset}}'
    ])
);

function getExportDatabaseCommand($envPath, $destination) {
    $usernameArg = '--user=$SS_DATABASE_USERNAME';
    $passwordArg = '--password=$SS_DATABASE_PASSWORD';
    $hostArg = '--host=$SS_DATABASE_SERVER';
    $databaseArg = '$SS_DATABASE_PREFIX$SS_DATABASE_NAME';

    $loadEnvCmd = "export $(grep -v '^#' {$envPath} | xargs)";

    $exportDbCmd = "mysqldump {{mysqldump_args}} {$usernameArg} {$passwordArg} {$hostArg} {$databaseArg} | gzip > {$destination}";
    return "{$loadEnvCmd} && {$exportDbCmd}";
}

function getImportDatabaseCommand($envPath, $source) {
    $usernameArg = '--user=$SS_DATABASE_USERNAME';
    $passwordArg = '--password=$SS_DATABASE_PASSWORD';
    $hostArg = '--host=$SS_DATABASE_SERVER';
    $databaseArg = '$SS_DATABASE_PREFIX$SS_DATABASE_NAME';

    $loadEnvCmd = "export $(grep -v '^#' {$envPath} | xargs)";

    $createDbArg = "--execute='create database if not exists `'{$databaseArg}'`;'";
    $createDbCmd = "mysql {{mysql_args}} {$usernameArg} {$passwordArg} {$hostArg} {$createDbArg}";

    $importDbCmd = "gunzip < {$source} | mysql {{mysql_args}} {$usernameArg} {$passwordArg} {$hostArg} {$databaseArg}";

    return "{$loadEnvCmd} && {$createDbCmd} && {$importDbCmd}";
}
