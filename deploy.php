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
inventory('deploy/hosts.yml');

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
