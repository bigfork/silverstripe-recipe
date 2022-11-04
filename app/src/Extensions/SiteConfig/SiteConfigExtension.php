<?php

namespace App\Extensions\SiteConfig;

use SilverStripe\Core\Extension;
use SilverStripe\Forms\EmailField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\HeaderField;
use SilverStripe\Forms\TextareaField;
use SilverStripe\Forms\TextField;

class SiteConfigExtension extends Extension
{
    private static $db = [
        'EmailAddress' => 'Varchar',
        'Telephone'    => 'Varchar',
        'StartOfHead'  => 'Text',
        'EndOfHead'    => 'Text',
        'StartOfBody'  => 'Text',
        'EndOfBody'    => 'Text',
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

        $fields->addFieldsToTab(
            'Root.Scripts',
            [
                TextareaField::create('StartOfHead', 'Start of head'),
                TextareaField::create('EndOfHead', 'End of head'),
                TextareaField::create('StartOfBody', 'Start of body'),
                TextareaField::create('EndOfBody', 'End of body'),
            ]
        );
    }
}
