<?php

use App\Model\Page as NamespacedPage;
use App\Model\Page as NamespacedPageController;

class Page extends NamespacedPage
{
    private static $hide_ancestor = NamespacedPage::class;
}

class PageController extends NamespacedPageController
{
}
