<?php

namespace App\Model\Elements;

use App\Extensions\Elemental\BeforeAfterContentExtension;
use Bummzack\SortableFile\Forms\SortableUploadField;
use DNADesign\Elemental\Models\ElementContent;
use SilverStripe\Assets\Image;
use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\ArrayList;
use SilverStripe\ORM\DataList;
use SilverStripe\ORM\FieldType\DBField;
use SilverStripe\ORM\FieldType\DBHTMLText;
use SilverStripe\ORM\ManyManyList;
use SilverStripe\ORM\UnsavedRelationList;

class ElementFullWidthImage extends ElementContent
{
    private static string $table_name = 'ElementFullWidthImage';

    private static array $many_many = [
        'Images' => Image::class,
    ];

    private static array $many_many_extraFields = [
        'Images' => [
            'SortOrder' => 'Int',
        ],
    ];

    private static array $owns = [
        'Images',
    ];

    private static string $singular_name = 'full width image block';

    private static string $plural_name = 'full width image blocks';

    private static string $description = 'Full width image block';

    private static string $icon = 'font-icon-block-carousel';

    private static array $extensions = [
        BeforeAfterContentExtension::class
    ];

    public function getCMSFields(): FieldList
    {
        $this->beforeUpdateCMSFields(
            function (FieldList $fields) {
                $fields->addFieldsToTab(
                    'Root.Images',
                    [
                        SortableUploadField::create('Images', 'Images')
                            ->setAllowedFileCategories('image'),
                    ]
                );
            }
        );

        return parent::getCMSFields();
    }

    public function getType(): string
    {
        return 'Full width image';
    }

    protected function provideBlockSchema(): array
    {
        $blockSchema = parent::provideBlockSchema();

        $images = $this->Images();
        /** @var Image $firstImage */
        $firstImage = $this->Images()->first();

        if ($firstImage && $firstImage->exists() && $firstImage->getIsImage()) {
            $blockSchema['fileURL'] = $firstImage->CMSThumbnail()->getURL();
            $blockSchema['fileTitle'] = $firstImage->getTitle();
        }

        $imagesCount = $this->Images()->count();
        $plural = $imagesCount === 1 ? '' : 's';

        $blockSchema['content'] = "Currently shows {$imagesCount} image{$plural}";

        return $blockSchema;
    }

    public function Images(): UnsavedRelationList|ManyManyList|ArrayList|DataList
    {
        return $this->getManyManyComponents('Images')->sort('SortOrder ASC');
    }
}
