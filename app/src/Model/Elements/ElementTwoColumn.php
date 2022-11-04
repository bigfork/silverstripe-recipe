<?php

namespace App\Model\Elements;

use App\Extensions\Elemental\BeforeAfterContentExtension;
use DNADesign\Elemental\Models\BaseElement;
use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\Assets\Image;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\FieldGroup;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\HeaderField;
use SilverStripe\Forms\HTMLEditor\HTMLEditorField;
use SilverStripe\ORM\FieldType\DBEnum;
use SilverStripe\ORM\FieldType\DBField;
use SilverStripe\ORM\FieldType\DBHTMLText;
use UncleCheese\DisplayLogic\Forms\Wrapper;

/**
 * @method Image LeftColumnImage()
 * @method Image RightColumnImage()
 * @property string|null $LeftColumnContent
 * @property string|null $LeftColumnType
 * @property string|null $RightColumnContent
 * @property string|null $RightColumnType
 * @property mixed|null $LeftColumnImageID
 */
class ElementTwoColumn extends BaseElement
{
    private static string $table_name = 'ElementTwoColumn';

    private static array $db = [
        'LeftColumnType'       => 'Enum(array("Text", "Image"), "Text")',
        'LeftColumnContent'    => 'HTMLText',
        'LeftColumnImageCrop'  => 'Boolean',
        'RightColumnType'      => 'Enum(array("Text", "Image"), "Image")',
        'RightColumnContent'   => 'HTMLText',
        'RightColumnImageCrop' => 'Boolean',
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

    private static array $extensions = [
        BeforeAfterContentExtension::class
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
                        'LeftColumnType',
                        'LeftColumnContent',
                        'LeftColumnImage',
                        'LeftColumnImageCrop',
                        'RightColumnType',
                        'RightColumnContent',
                        'RightColumnImage',
                        'RightColumnImageCrop',
                    ]
                );

                /** @var DBEnum $leftColumnTypeField */
                $leftColumnTypeField = $this->dbObject('LeftColumnType');
                /** @var DBEnum $rightColumnTypeField */
                $rightColumnTypeField = $this->dbObject('RightColumnType');

                $fields->addFieldsToTab(
                    'Root.Main',
                    [
                        HeaderField::create('LeftColumn', 'Left column'),
                        DropdownField::create('LeftColumnType', 'Left column type', $leftColumnTypeField->enumValues()),
                        $leftContent = HTMLEditorField::create('LeftColumnContent', 'Left column content'),
                        $leftImage = Wrapper::create(
                            UploadField::create('LeftColumnImage', 'Left column image')
                                ->setAllowedFileCategories('image'),
                            FieldGroup::create(
                                'Crop image',
                                CheckboxField::create('LeftColumnImageCrop', 'Check to crop image to a fixed size')
                            )
                        )
                    ]
                );
                $leftContent->displayIf('LeftColumnType')->isEqualTo('Text');
                $leftImage->displayIf('LeftColumnType')->isNotEqualTo('Text');

                $fields->addFieldsToTab(
                    'Root.Main',
                    [
                        HeaderField::create('RightColumn', 'Right column'),
                        DropdownField::create('RightColumnType', 'Type', $rightColumnTypeField->enumValues()),
                        $rightContent = HTMLEditorField::create('RightColumnContent', 'Right column content'),
                        $rightImage = Wrapper::create(
                            UploadField::create('RightColumnImage', 'Right column image')
                                ->setAllowedFileCategories('image'),
                            FieldGroup::create(
                                'Crop image',
                                CheckboxField::create('RightColumnImageCrop', 'Check to crop image to a fixed size')
                            )
                        ),
                    ]
                );
                $rightContent->displayIf('RightColumnType')->isEqualTo('Text');
                $rightImage->displayIf('RightColumnType')->isNotEqualTo('Text');
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
