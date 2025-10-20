<?php

namespace App\Extensions\Elemental;

use AdrHumphreys\TextDropdownField\TextDropdownField;
use SilverStripe\Core\Convert;
use SilverStripe\Core\Extension;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\Tab;
use SilverStripe\View\HTML;

class BaseElementExtension extends Extension
{
    private static array $db = [
        'HeadingLevel' => 'Enum(array("h1", "h2", "h3", "h4", "h5", "h6", "hidden"), "h2")',
    ];

    private static array $casting = [
        'TitleTag' => 'HTMLFragment',
    ];

    protected array $headingLevels = [
        'h1' => 'Heading 1',
        'h2' => 'Heading 2',
        'h3' => 'Heading 3',
        'h4' => 'Heading 4',
        'h5' => 'Heading 5',
        'h6' => 'Heading 6',
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
            TextDropdownField::create('TitleAndHeadingLevel', 'Title', 'Title', 'HeadingLevel', $this->headingLevels)
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
        if ($this->owner->HeadingLevel === 'hidden' || empty($this->getOwner()->Title)) {
            return '';
        }

        return HTML::createTag(
            $this->owner->HeadingLevel,
            ['class' => $extraClass],
            Convert::raw2xml($this->owner->Title)
        );
    }
}
