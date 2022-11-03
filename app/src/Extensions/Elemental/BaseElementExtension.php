<?php

namespace App\Extensions\Elemental;

use AdrHumphreys\TextDropdownField\TextDropdownField;
use SilverStripe\Core\Convert;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\Tab;
use SilverStripe\ORM\DataExtension;
use SilverStripe\View\HTML;

class BaseElementExtension extends DataExtension
{
    private static array $db = [
        'HeadingLevel' => 'Enum(array("h1", "h2", "h3", "hidden"), "h2")',
    ];

    private static array $casting = [
        'TitleTag' => 'HTMLFragment'
    ];

    private array $HeadingLevels = [
        'h1'     => 'Heading 1',
        'h2'     => 'Heading 2',
        'h3'     => 'Heading 3',
        'hidden' => 'Hide title',
    ];

    public function updateCMSFields(FieldList $fields): void
    {
        $fields->removeByName(
            [
                'ExtraClass',
                'HeadingLevel',
                'Title',
                'Settings',
            ]
        );

        /** @var Tab $tab */
        $tab = $fields->fieldByName('Root.Main');
        $tab->getChildren()->unshift(
            TextDropdownField::create('TitleAndHeadingLevel', 'Title', 'Title', 'HeadingLevel', $this->HeadingLevels)
                ->setName('TitleAndHeadingLevel')
        );

        // Move history tab last
        if ($historyTab = $fields->fieldByName('Root.History')) {
            $fields->removeByName($historyTab->getName());
            $fields->fieldByName('Root')->push($historyTab);
        }
    }

    public function TitleTag(string $extraClass = ''): string
    {
        if ($this->owner->HeadingLevel === 'hidden') {
            return '';
        }

        return HTML::createTag(
            $this->owner->HeadingLevel,
            ['class' => $extraClass],
            Convert::raw2xml($this->owner->Title)
        );
    }
}
