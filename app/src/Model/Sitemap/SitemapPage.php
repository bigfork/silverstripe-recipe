<?php

namespace App\Model\Sitemap;

use App\Model\Page;
use SilverStripe\View\TemplateGlobalProvider;

class SitemapPage extends Page implements TemplateGlobalProvider
{
    private static $table_name = 'SitemapPage';

    private static $description = 'Page containing a sitemap';

    private static $icon_class = 'font-icon-p-list';

    private static $defaults = [
        'ShowInMenus' => false,
    ];

    public function canCreate($member = null, $context = [])
    {
        if (static::get()->count()) {
            return false;
        }

        return parent::canCreate($member, $context);
    }

    public static function get_template_global_variables()
    {
        return [
            'SitemapPage' => 'get_one_cached',
        ];
    }
}
