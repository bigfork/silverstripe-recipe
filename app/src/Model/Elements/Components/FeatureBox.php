<?php

namespace App\Model\Elements\Components;

use App\Model\Elements\ElementFeatureBoxes;
use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\Assets\File;
use SilverStripe\Assets\Image;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\OptionsetField;
use SilverStripe\Forms\TextareaField;
use SilverStripe\Forms\TextField;
use SilverStripe\Forms\TreeDropdownField;
use SilverStripe\ORM\DataObject;
use SilverStripe\Security\Permission;
use SilverStripe\Versioned\Versioned;
use UncleCheese\DisplayLogic\Forms\Wrapper;

/**
 * @property string|null $LinkType
 * @property string|null $LinkURL
 * @method SiteTree LinkedPage()
 * @method File LinkedFile()
 */
class FeatureBox extends DataObject
{
    private static string $table_name = 'ElementFeatureBoxes_FeatureBox';

    private static array $db = [
        'Sort'       => 'Int',
        'Title'      => 'Varchar',
        'Content'    => 'Text',
        'LinkText'   => 'Varchar',
        'LinkType'   => 'Varchar',
        'LinkURL'    => 'Text',
        'LinkTarget' => 'Varchar',
    ];

    private static array $has_one = [
        'FeatureBoxBlock' => ElementFeatureBoxes::class,
        'Image'           => Image::class,
        'LinkedPage'      => SiteTree::class,
        'LinkedFile'      => File::class,
    ];

    private static array $owns = [
        'Image',
        'LinkedFile',
    ];

    private static array $defaults = [
        'LinkType' => 'Page',
    ];

    private static array $summary_fields = [
        'Image.CMSThumbnail' => 'Image',
        'Title'              => 'Title',
        'LinkType'           => 'Link type',
    ];

    private static string $default_sort = 'Sort ASC';

    private static string $singular_name = 'Feature Box';

    private static string $plural_name = 'Feature Boxes';

    private static array $extensions = [
        Versioned::class,
    ];

    public function canView($member = null): bool|int
    {
        return Permission::check('CMS_ACCESS_CMSMain', 'any', $member);
    }

    public function canEdit($member = null): bool|int
    {
        return Permission::check('CMS_ACCESS_CMSMain', 'any', $member);
    }

    public function canDelete($member = null): bool|int
    {
        return Permission::check('CMS_ACCESS_CMSMain', 'any', $member);
    }

    public function canCreate($member = null, $context = []): bool|int
    {
        return Permission::check('CMS_ACCESS_CMSMain', 'any', $member);
    }

    public function getCMSFields(): FieldList
    {
        $this->beforeUpdateCMSFields(
            function (FieldList $fields) {
                $fields->removeByName(
                    [
                        'Content',
                        'FeatureBoxBlockID',
                        'Image',
                        'LinkedFile',
                        'LinkedPageID',
                        'LinkTarget',
                        'LinkText',
                        'LinkType',
                        'LinkURL',
                        'Sort',
                        'Title',
                    ]
                );

                $fields->addFieldsToTab(
                    'Root.Main',
                    [
                        UploadField::create('Image', 'Image')
                            ->setAllowedFileCategories('image')
                            ->setFolderName('feature-boxes'),
                        TextField::create('Title', 'Title'),
                        TextareaField::create('Content', 'Content'),
                        OptionsetField::create(
                            'LinkType',
                            'Link type',
                            [
                                'None' => 'No link',
                                'Page' => 'Link to a page on this site',
                                'File' => 'Link to a file on this site',
                                'URL'  => 'Link to another website'
                            ]
                        ),
                        TextField::create('LinkText', 'Link text')
                            ->displayIf('LinkType')
                            ->isNotEqualTo('None')
                            ->end(),
                        Wrapper::create(
                            TreeDropdownField::create('LinkedPageID', 'Linked page', SiteTree::class)
                                ->setTitleField('MenuTitle')
                        )->displayIf('LinkType')
                            ->isEqualTo('Page')
                            ->end(),
                        Wrapper::create(
                            UploadField::create('LinkedFile', 'Linked file')
                                ->setFolderName('Files')
                        )->displayIf('LinkType')
                            ->isEqualTo('File')
                            ->end(),
                        Wrapper::create(
                            TextField::create('LinkURL', 'Linked page')
                                ->setDescription('Please include the "https://" prefix')
                        )->displayIf('LinkType')
                            ->isEqualTo('URL')
                            ->end(),
                        Wrapper::create(
                            DropdownField::create('LinkTarget', 'Link target', [
                                '_blank' => 'Open in a new window'
                            ])->setEmptyString('Open in the same window')
                        )->displayIf('LinkType')
                            ->isNotEqualTo('None')
                            ->end(),
                    ]
                );
            }
        );

        return parent::getCMSFields();
    }

    protected function onBeforeWrite()
    {
        if (!$this->Sort) {
            $this->Sort = self::get()->max('Sort') + 1;
        }

        parent::onBeforeWrite();
    }

    public function Link(): string
    {
        switch ($this->LinkType) {
            case 'Page':
                $page = $this->LinkedPage();
                if ($page->exists()) {
                    return $page->Link();
                }
                break;
            case 'File':
                $file = $this->LinkedFile();
                if ($file->exists()) {
                    return $file->Link();
                }
                break;
            case 'URL':
                return $this->LinkURL;
        }

        return '';
    }
}
