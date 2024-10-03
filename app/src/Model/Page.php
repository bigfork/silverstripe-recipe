<?php

namespace App\Model;

use App\Control\PageController;
use SilverStripe\CMS\Controllers\RootURLController;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Control\Controller;
use SilverStripe\Core\ClassInfo;
use SilverStripe\Core\Config\Config;
use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\ArrayList;
use SilverStripe\ORM\DataObject;
use SilverStripe\Security\Permission;
use SilverStripe\SiteConfig\SiteConfig;

class Page extends SiteTree
{
    private static $table_name = 'Page';

    private static $icon_class = 'font-icon-p-alt-2';

    public static function get_one_cached()
    {
        return DataObject::get_one(static::class);
    }

    public function getCMSFields()
    {
        $this->beforeUpdateCMSFields(function (FieldList $fields) {
            $homeURL = Config::inst()->get(RootURLController::class, 'default_homepage_link');

            if (!Permission::check('ADMIN') && $this->URLSegment === $homeURL) {
                $fields->removeByName('URLSegment');
            }
        });

        return parent::getCMSFields();
    }

    public function getSettingsFields()
    {
        $this->beforeExtending('updateSettingsFields', function (FieldList $fields) {
            // Hide ShowInSearch checkbox if we don't have a search
            $fields->removeByName('ShowInSearch');
        });

        return parent::getSettingsFields();
    }

    /**
     * Returns the controller class name for this page type. If a matching subclass of
     * PageController exists, use that. Otherwise default to the base namespaced controller.
     *
     * This is required as SiteTree::getControllerName() doesn't traverse sideways across
     * namespaces (i.e from \Model to \Control) when looking for a controller.
     *
     * @return string
     */
    public function getControllerName()
    {
        $current = static::class;
        $ancestry = ClassInfo::ancestry($current);
        $controller = null;
        while ($class = array_pop($ancestry)) {
            if ($class === self::class) {
                break;
            }
            if (class_exists($candidate = sprintf('%sController', $class))) {
                $controller = $candidate;
                break;
            }
            $candidate = sprintf('%sController', str_replace('\\Model\\', '\\Control\\', $class));
            if (class_exists($candidate)) {
                $controller = $candidate;
                break;
            }
        }
        if ($controller) {
            return $controller;
        }
        return PageController::class;
    }

    public function getBreadcrumbItems($maxDepth = 20, $stopAtPageType = false, $showHidden = false): ArrayList
    {
        $items = parent::getBreadcrumbItems($maxDepth, $stopAtPageType, $showHidden);
        $homeSlug = RootURLController::config()->get('default_homepage_link');

        if ($this->URLSegment !== $homeSlug) {
            $home = Page::get()->filter('URLSegment', $homeSlug)->first();

            if ($home) {
                $items->unshift($home);
            }
        }

        if (Controller::has_curr()) {
            $controller = Controller::curr();
            $controller->invokeWithExtensions('updateBreadcrumbItems', $items);
        }

        return $items;
    }

    public function MetaComponents()
    {
        $tags = parent::MetaComponents();

        // We hardcode this in Head.ss
        unset($tags['contentType']);

        $config = SiteConfig::current_site_config();

        if (!isset($tags['canonical'])) {
            $tags['canonical'] = [
                'tag'        => 'link',
                'attributes' => [
                    'rel'  => 'canonical',
                    'href' => $this->AbsoluteLink(),
                ],
            ];
        }

        if (!isset($tags['og:title'])) {
            $tags['og:title'] = [
                'tag'        => 'meta',
                'attributes' => [
                    'property' => 'og:title',
                    'content'  => $this->MetaTitle ?: "{$this->Title}",
                ],
            ];
        }

        if (!isset($tags['og:type'])) {
            $tags['og:type'] = [
                'tag'        => 'meta',
                'attributes' => [
                    'property' => 'og:type',
                    'content'  => 'website',
                ],
            ];
        }

        if (!isset($tags['og:url'])) {
            $tags['og:url'] = [
                'tag'        => 'meta',
                'attributes' => [
                    'property' => 'og:url',
                    'content'  => $this->AbsoluteLink(),
                ],
            ];
        }

        if (!isset($tags['og:description'])) {
            $tags['og:description'] = [
                'tag'        => 'meta',
                'attributes' => [
                    'property' => 'og:description',
                    'content'  => $tags['description']['attributes']['content'] ?? $this->MetaDescription,
                ],
            ];
        }

        if (!isset($tags['og:site_name'])) {
            $tags['og:site_name'] = [
                'tag'        => 'meta',
                'attributes' => [
                    'property' => 'og:site_name',
                    'content'  => $config->Title ?: 'Bigfork',
                ],
            ];
        }

        if (!isset($tags['og:locale'])) {
            $tags['og:locale'] = [
                'tag'        => 'meta',
                'attributes' => [
                    'property' => 'og:locale',
                    'content'  => 'en_GB',
                ],
            ];
        }

        if (!isset($tags['twitter:title'])) {
            $tags['twitter:title'] = [
                'tag'        => 'meta',
                'attributes' => [
                    'name'    => 'twitter:title',
                    'content' => $this->MetaTitle ?: "{$this->Title}",
                ],
            ];
        }

        if (!isset($tags['twitter:description'])) {
            $tags['twitter:description'] = [
                'tag'        => 'meta',
                'attributes' => [
                    'name'    => 'twitter:description',
                    'content' => $tags['description']['attributes']['content'] ?? $this->MetaDescription,
                ],
            ];
        }

        return $tags;
    }
}
