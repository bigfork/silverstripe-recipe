<?php

namespace App\Control;

use SilverStripe\CMS\Controllers\ContentController;
use SilverStripe\View\Requirements;

class PageController extends ContentController
{
    public function init()
    {
        parent::init();

        Requirements::set_force_js_to_bottom(true);
        Requirements::themedJavascript('js/app.min.js');
    }
}
