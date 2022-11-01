<?php

namespace App\Extensions\Blog;

use SilverStripe\Core\Extension;
use SilverStripe\Forms\FieldList;

class BlogPostExtension extends Extension
{
    public function updateCMSFields(FieldList $fields): void
    {
        $summaryHTMLEditor = $fields->dataFieldByName('Summary');
        $fields->insertBefore('CustomSummary', $summaryHTMLEditor);
        $fields->removeByName('CustomSummary');
    }
}
