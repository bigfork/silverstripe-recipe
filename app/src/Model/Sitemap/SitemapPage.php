<?php

namespace App\Model\Sitemap;

use App\Model\Page;
use SilverStripe\View\TemplateGlobalProvider;

class SitemapPage extends Page implements TemplateGlobalProvider
{
    private static string $table_name = 'SitemapPage';

    private static string $description = 'Page containing a sitemap';

    private static string $icon_class = 'font-icon-p-list';

    private static array $defaults = [
        'ShowInMenus' => false,
    ];

    public function canCreate($member = null, $context = []): bool
    {
        if (static::get()->count()) {
            return false;
        }

        return parent::canCreate($member, $context);
    }

    public static function get_template_global_variables(): array
    {
        return [
            'SitemapPage' => 'get_one_cached',
        ];
    }
}
