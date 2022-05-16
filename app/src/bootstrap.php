<?php

use SilverStripe\CMS\Controllers\ContentController;
use SilverStripe\CMS\Model\SiteTree;

class Page extends SiteTree
{
    private static $table_name = 'SilverStripe_Page';
}

class PageController extends ContentController
{
}
