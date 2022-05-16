<?php

namespace App\Control;

use PageController as SilverStripePageController;
use SilverStripe\View\Requirements;

class PageController extends SilverStripePageController
{
    protected function init()
    {
        parent::init();

        Requirements::set_force_js_to_bottom(true);
        Requirements::themedJavascript('dist/js/app.js');
    }
}
