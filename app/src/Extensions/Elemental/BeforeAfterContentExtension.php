<?php

namespace App\Extensions\Elemental;

use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\HTMLEditor\HTMLEditorField;
use SilverStripe\Forms\Tab;
use SilverStripe\ORM\DataExtension;

class BeforeAfterContentExtension extends DataExtension
{
    private static array $db = [
        'AfterHTML' => 'HTMLText',
    ];

    public function updateCMSFields(FieldList $fields): void
    {
        if ($tab = $fields->fieldByName('Root.Main')) {
            $tab->setTitle('Before');
        }

        $fields->insertBefore('History', Tab::create('After'));
        $fields->addFieldsToTab(
            'Root.After',
            [
                HTMLEditorField::create('AfterHTML', 'Content'),
            ]
        );
    }
}
