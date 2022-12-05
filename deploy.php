<?php

namespace Deployer;

require 'recipe/common.php';
require 'deploy/recipe/silverstripe.php';

// Number of releases to keep
set('keep_releases', 5);

// [Optional] Allocate tty for git clone. Default value is false
set('git_tty', true);

// Shared files/dirs between deploys
set('shared_files', [
    '.env',
    'debug.log'
]);
set('shared_dirs', [
    'public/assets',
    'public/.well-known'
]);

// Writable dirs by web server
set('writable_dirs', [
    'public/assets',
    'silverstripe-cache'
]);
set('allow_anonymous_stats', false);

// Hosts
set('default_stage', 'staging');
inventory(__DIR__ . '/deploy/hosts.yml');

desc('Deploy your project');
task('deploy', function() {
    invoke('deploy:info');
    invoke('deploy:prepare');
    invoke('deploy:lock');
    invoke('deploy:release');
    invoke('deploy:update_code');
    invoke('silverstripe:create_dotenv');
    invoke('silverstripe:create_cache_dir');
    invoke('deploy:shared');
    invoke('deploy:writable');
    invoke('deploy:vendors');
    invoke('silverstripe:vendor_expose');
    invoke('silverstripe:dev_build');
    invoke('deploy:clear_paths');
    invoke('deploy:symlink');
    invoke('deploy:unlock');
    invoke('cleanup');
    invoke('success');
});

// [Optional] If deploy fails automatically unlock.
after('deploy:failed', 'deploy:unlock');

task('clear_cloudflare_cache', function () {
    $config = Context::get()->getHost()->getConfig();
    $zone_id_key = 'zone_id';
    $api_key_key = 'api_key';
    $zone_id_value = $config->has($zone_id_key) ? $config->get($zone_id_key) : null;
    $api_key_value = $config->has($api_key_key) ? $config->get($api_key_key) : null;

    if (!empty($zone_id_value) && !empty($api_key_value)) {
        try {
            ob_start();
            $head = [];
            $head[] = 'Content-Type: application/json';
            $head[] = "Authorization: Bearer {$api_key_value}";
            $head[] = 'cache-control: no-cache';

            $url = "https://api.cloudflare.com/client/v4/zones/{$zone_id_value}/purge_cache";
            $purge = ['purge_everything' => true];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($purge));
            curl_exec($ch);
            curl_close($ch);
            ob_clean();
        } catch (Exception $e) {
            print($e);
        }
    }
});
before('success', 'clear_cloudflare_cache');
