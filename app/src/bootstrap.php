<?php

use SilverStripe\CMS\Controllers\ContentController;
use SilverStripe\CMS\Model\SiteTree;

class Page extends SiteTree
{
    private static $table_name = 'SilverStripe_Page';

    public function canCreate($member = null, $context = [])
    {
        if (static::class === \App\Model\Page::class) {
            return false;
        }

        return parent::canCreate($member, $context);
    }
}

class PageController extends ContentController
{
}
