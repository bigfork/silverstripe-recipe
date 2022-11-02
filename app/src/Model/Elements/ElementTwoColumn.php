<?php

namespace App\Model\Elements;

use DNADesign\Elemental\Models\ElementContent;
use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\Assets\Image;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\HeaderField;
use SilverStripe\Forms\HTMLEditor\HTMLEditorField;
use SilverStripe\ORM\FieldType\DBEnum;
use SilverStripe\ORM\FieldType\DBField;
use SilverStripe\ORM\FieldType\DBHTMLText;

/**
 * @method Image LeftColumnImage()
 * @method Image RightColumnImage()
 * @property string|null $LeftColumnContent
 * @property string|null $LeftColumnType
 * @property string|null $RightColumnContent
 * @property string|null $RightColumnType
 * @property mixed|null $LeftColumnImageID
 */
class ElementTwoColumn extends ElementContent
{
    private static string $table_name = 'ElementTwoColumn';

    private static array $db = [
        'LeftColumnType'       => 'Enum(array("Text", "Image"), "Text")',
        'LeftColumnContent'    => 'HTMLText',
        'RightColumnType'      => 'Enum(array("Text", "Image"), "Image")',
        'RightColumnContent'   => 'HTMLText',
    ];

    private static array $has_one = [
        'LeftColumnImage'  => Image::class,
        'RightColumnImage' => Image::class,
    ];

    private static array $owns = [
        'LeftColumnImage',
        'RightColumnImage',
    ];

    private static array $defaults = [
        'LeftColumnType'  => 'Text',
        'RightColumnType' => 'Image',
    ];

    private static string $singular_name = 'two column block';

    private static string $plural_name = 'two column blocks';

    private static string $description = 'Two column block';

    private static string $icon = 'font-icon-block-layout-8';

    public function getCMSFields(): FieldList
    {
        $this->beforeUpdateCMSFields(
            function (FieldList $fields) {
                $fields->removeByName(
                    [
                        'HTML',
                        'LeftColumnType',
                        'LeftColumnContent',
                        'LeftColumnImage',
                        'RightColumnType',
                        'RightColumnContent',
                        'RightColumnImage',
                    ]
                );

                /** @var DBEnum $leftColumnTypeField */
                $leftColumnTypeField = $this->dbObject('LeftColumnType');
                /** @var DBEnum $rightColumnTypeField */
                $rightColumnTypeField = $this->dbObject('RightColumnType');

                $fields->addFieldsToTab(
                    'Root.Main',
                    [
                        HeaderField::create('LeftColumnHeader', 'Left column'),
                        DropdownField::create('LeftColumnType', 'Type', $leftColumnTypeField->enumValues()),
                        HTMLEditorField::create('LeftColumnContent', 'Content')
                            ->displayIf('LeftColumnType')->isEqualTo('Text')->end(),
                        UploadField::create('LeftColumnImage', 'Image')
                            ->setAllowedFileCategories('image')
                            ->displayIf('LeftColumnType')->isNotEqualTo('Text')->end(),

                        HeaderField::create('RightColumnHeader', 'Right column'),
                        DropdownField::create('RightColumnType', 'Type', $rightColumnTypeField->enumValues()),
                        HTMLEditorField::create('RightColumnContent', 'Content')
                            ->displayIf('RightColumnType')->isEqualTo('Text')->end(),
                        UploadField::create('RightColumnImage', 'Image')
                            ->setAllowedFileCategories('image')
                            ->displayIf('RightColumnType')->isNotEqualTo('Text')->end(),
                    ]
                );
            }
        );

        return parent::getCMSFields();
    }

    public function getType(): string
    {
        return 'Two column';
    }

    protected function provideBlockSchema(): array
    {
        $blockSchema = parent::provideBlockSchema();

        if ($this->LeftColumnType === 'Image' || $this->RightColumnType === 'Image') {
            $image = $this->RightColumnImage();

            if ($this->LeftColumnImageID) {
                $image = $this->LeftColumnImage();
            }

            if ($image->exists() && $image->getIsImage()) {
                $blockSchema['fileURL'] = $image->CMSThumbnail()->getURL();
                $blockSchema['fileTitle'] = $image->getTitle();
            }
        }

        /** @var DBHTMLText $content */
        $content = DBField::create_field('HTMLFragment', $this->LeftColumnContent ?? $this->RightColumnContent);
        $blockSchema['content'] = $content->summary();

        return $blockSchema;
    }
}
