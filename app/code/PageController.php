<?php

use SilverStripe\CMS\Controllers\ContentController;
use SilverStripe\Core\Config\Config;
use SilverStripe\View\Requirements;

class PageController extends ContentController
{
    public function init()
    {
        parent::init();

        $themeDir = 'themes/' . Config::inst()->get('SSViewer', 'theme');
        Requirements::set_force_js_to_bottom(true);
        Requirements::javascript($themeDir . '/js/app.min.js');
    }
}
