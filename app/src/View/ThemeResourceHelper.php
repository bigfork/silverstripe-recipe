<?php

namespace App\View;

use SilverStripe\Core\Manifest\ModuleResourceLoader;
use SilverStripe\View\TemplateGlobalProvider;
use SilverStripe\View\ThemeResourceLoader;

class ThemeResourceHelper implements TemplateGlobalProvider
{
    public static function themeResourcePath(string $resource): ?string
    {
        return ThemeResourceLoader::inst()->findThemedResource($resource);
    }

    public static function themeResourceURL(string $resource): ?string
    {
        $path = static::themeResourcePath($resource);
        if (!$path) {
            return '';
        }

        return ModuleResourceLoader::singleton()->resolveURL($path);
    }

    public static function get_template_global_variables(): array
    {
        return [
            'themeResourcePath',
            'themeResourceURL'
        ];
    }
}
