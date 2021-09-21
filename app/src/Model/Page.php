<?php

namespace App\Model;

use App\Control\PageController;
use SilverStripe\CMS\Controllers\RootURLController;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Core\ClassInfo;
use SilverStripe\Core\Config\Config;
use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\DataObject;
use SilverStripe\Security\Permission;

class Page extends SiteTree
{
    private static $table_name = 'Page';

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
}
