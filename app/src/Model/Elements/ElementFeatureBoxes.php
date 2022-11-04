<?php

namespace App\Model\Elements;

use App\Extensions\Elemental\BeforeAfterContentExtension;
use App\Forms\GridField\GridFieldConfig_OrderableRecordEditor;
use App\Model\Elements\Components\FeatureBox;
use DNADesign\Elemental\Models\BaseElement;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\ORM\HasManyList;

/**
 * @method HasManyList FeatureBoxes()
 */
class ElementFeatureBoxes extends BaseElement
{
    private static string $table_name = 'ElementFeatureBoxes';

    private static array $has_many = [
        'FeatureBoxes' => FeatureBox::class,
    ];

    private static array $owns = [
        'FeatureBoxes',
    ];

    private static array $cascade_deletes = [
        'FeatureBoxes',
    ];

    private static array $cascade_duplicates = [
        'FeatureBoxes',
    ];

    private static string $singular_name = 'feature boxes block';

    private static string $plural_name = 'feature box blocks';

    private static string $description = 'Feature boxes block';

    private static string $icon = 'font-icon-block-layout-2';

    private static array $extensions = [
        BeforeAfterContentExtension::class
    ];

    public function getCMSFields(): FieldList
    {
        $this->beforeUpdateCMSFields(
            function (FieldList $fields) {
                $fields->removeByName(['FeatureBoxes']);
                $fields->addFieldToTab(
                    'Root.Main',
                    GridField::create(
                        'FeatureBoxes',
                        'Feature boxes',
                        $this->FeatureBoxes(),
                        GridFieldConfig_OrderableRecordEditor::create()
                    )
                );
            }
        );

        return parent::getCMSFields();
    }

    public function getType(): string
    {
        return 'Feature boxes';
    }

    public function getSummary(): string
    {
        $plural = $this->FeatureBoxes()->count() === 1 ? '' : 'es';
        return "Currently shows {$this->FeatureBoxes()->count()} feature box{$plural}";
    }
}
