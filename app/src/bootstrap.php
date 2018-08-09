<?php

use App\Control\PageController as NamespacedPageController;
use App\Model\Page as NamespacedPage;

class Page extends NamespacedPage
{
    private static $hide_ancestor = NamespacedPage::class;

    private static $table_name = 'SilverStripe_Page';
}

class PageController extends NamespacedPageController
{
}
