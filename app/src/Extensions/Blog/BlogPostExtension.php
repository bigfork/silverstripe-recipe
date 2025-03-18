<?php

namespace App\Extensions\Blog;

use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\Blog\Model\Blog;
use SilverStripe\Core\Extension;
use SilverStripe\Forms\FieldList;

class BlogPostExtension extends Extension
{
    public function canCreate($member = null, $context = []): ?bool
    {
        $parent = $context['Parent'] ?? null;
        if (!$parent instanceof Blog) {
            return false;
        }

        return null;
    }

    public function updateCMSFields(FieldList $fields): void
    {
        // Move summary out of ToggleCompositeField
        $summaryHTMLEditor = $fields->dataFieldByName('Summary');
        $summaryHTMLEditor->setTitle('Post summary');
        $fields->insertBefore('CustomSummary', $summaryHTMLEditor);
        $fields->removeByName(['CustomSummary']);
    }
}
