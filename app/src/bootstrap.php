<?php

use App\Control\PageController as NamespacedPageController;
use App\Model\Page as NamespacedPage;

class Page extends NamespacedPage
{
    private static $hide_ancestor = NamespacedPage::class;
}

class PageController extends NamespacedPageController
{
}
