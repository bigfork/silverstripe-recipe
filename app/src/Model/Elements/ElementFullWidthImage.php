<?php

namespace App\Model\Elements;

use App\Extensions\Elemental\BeforeAfterContentExtension;
use Bummzack\SortableFile\Forms\SortableUploadField;
use DNADesign\Elemental\Models\BaseElement;
use SilverStripe\Assets\Image;
use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\ManyManyList;

/**
 * @method ManyManyList Images()
 */
class ElementFullWidthImage extends BaseElement
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
                $fields->removeByName(['Images']);
                $fields->addFieldsToTab(
                    'Root.Main',
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

        $images = $this->getSortedImages();
        /** @var Image $firstImage */
        $firstImage = $images->first();

        if ($firstImage && $firstImage->exists() && $firstImage->getIsImage()) {
            $blockSchema['fileURL'] = $firstImage->CMSThumbnail()->getURL();
            $blockSchema['fileTitle'] = $firstImage->getTitle();
        }

        $imagesCount = $images->count();
        $plural = $imagesCount === 1 ? '' : 's';

        $blockSchema['content'] = "Currently shows {$imagesCount} image{$plural}";

        return $blockSchema;
    }

    public function getSortedImages()
    {
        return $this->Images()->sort('SortOrder ASC');
    }
}
