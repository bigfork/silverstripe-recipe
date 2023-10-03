<?php

namespace App\Extensions\SiteConfig;

use SilverStripe\Core\Extension;
use SilverStripe\Forms\EmailField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TextField;

class SiteConfigExtension extends Extension
{
    private static $db = [
        'EmailAddress' => 'Varchar(255)',
        'Telephone' => 'Varchar(50)',
    ];

    public function updateCMSFields(FieldList $fields): void
    {
        $fields->removeByName(['Tagline']);

        $fields->addFieldsToTab(
            'Root.SiteSettings',
            [
                EmailField::create('EmailAddress', 'Email address'),
                TextField::create('Telephone', 'Telephone number'),
            ]
        );
    }
}
