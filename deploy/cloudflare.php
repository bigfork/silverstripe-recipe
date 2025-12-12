<?php

namespace Deployer;

use Deployer\Task\Context;

require 'contrib/cloudflare.php';

task('cloudflare:purge', function () {
    $config = Context::get()->getHost()->config();
    if (!$config->has('cloudflare') || empty(array_filter($config->get('cloudflare')))) {
        return;
    }

    set('cloudflare', $config->get('cloudflare'));
    invoke('deploy:cloudflare');
});
