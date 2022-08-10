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

class Page extends SiteTree
{
    private static $table_name = 'Page';

    private static $icon_class = 'font-icon-p-alt-2';

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

    public static function get_one_cached()
    {
        return DataObject::get_one(static::class);
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
}
