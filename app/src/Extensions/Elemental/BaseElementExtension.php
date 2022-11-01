<?php

namespace App\Extensions\Elemental;

use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\DataExtension;

class BaseElementExtension extends DataExtension
{
    public function updateCMSFields(FieldList $fields): void
    {
        $fields->removeByName(
            [
                'ExtraClass',
                'Settings',
            ]
        );
    }
}
