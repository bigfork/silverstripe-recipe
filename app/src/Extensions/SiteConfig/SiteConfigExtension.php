<?php

namespace App\Extensions\SiteConfig;

use SilverStripe\Core\Extension;
use SilverStripe\Forms\EmailField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\HeaderField;
use SilverStripe\Forms\TextField;
use SwiftDevLabs\CodeEditorField\Forms\CodeEditorField;

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
        $fields->removeByName(
            [
                'Tagline',
            ]
        );

        $fields->addFieldsToTab(
            'Root.SiteSettings',
            [
                EmailField::create('EmailAddress', 'Email address'),
                TextField::create('Telephone', 'Telephone number'),
            ]
        );

        $fields->addFieldsToTab(
            'Root.HeadAndFooterScripts',
            [
                HeaderField::create('HeadHeader', 'Head'),
                CodeEditorField::create('StartOfHead', 'Start of head'),
                CodeEditorField::create('EndOfHead', 'End of head'),
                HeaderField::create('BodyHeader', 'Body'),
                CodeEditorField::create('StartOfBody', 'Start of body'),
                CodeEditorField::create('EndOfBody', 'End of body'),
            ]
        );
    }
}
