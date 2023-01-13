<?php

namespace App\Extensions\CMS;

use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\FieldGroup;
use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\DataExtension;

class RedirectorPageExtension extends DataExtension
{
    private static array $db = [
        'NewWindow' => 'Boolean',
    ];

    public function updateCMSFields(FieldList $fields): void
    {
        $fields->addFieldsToTab(
            'Root.Main',
            [
                FieldGroup::create(
                    'Open in new window',
                    CheckboxField::create('NewWindow', 'Check to open URL in a new window')
                ),
            ]
        );
    }
}
