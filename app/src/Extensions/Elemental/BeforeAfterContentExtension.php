<?php

namespace App\Extensions\Elemental;

use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\HTMLEditor\HTMLEditorField;
use SilverStripe\Forms\Tab;
use SilverStripe\Forms\ToggleCompositeField;
use SilverStripe\ORM\DataExtension;

class BeforeAfterContentExtension extends DataExtension
{
    private static array $db = [
        'BeforeHTML' => 'HTMLText',
        'AfterHTML'  => 'HTMLText',
    ];

    public function updateCMSFields(FieldList $fields): void
    {
        /** @var Tab $main */
        $main = $fields->fieldByName('Root.Main');
        if (!$main) {
            return;
        }

        $fields->removeByName(['BeforeHTML', 'AfterHTML']);

        $main->insertAfter(
            'TitleAndHeadingLevel',
            ToggleCompositeField::create(
                'ContentBefore',
                'Content before',
                [
                    HTMLEditorField::create('BeforeHTML', 'Content')
                ]
            )
        );

        $main->push(
            ToggleCompositeField::create(
                'ContentAfter',
                'Content after',
                [
                    HTMLEditorField::create('AfterHTML', 'Content')
                ]
            )
        );
    }
}
