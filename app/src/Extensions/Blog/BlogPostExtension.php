<?php

namespace App\Extensions\Blog;

use SilverStripe\Core\Extension;
use SilverStripe\Forms\FieldList;

/**
 * Class BlogPostExtension
 * @package App\Extensions\Blog
 */
class BlogPostExtension extends Extension
{
    /**
     * @param FieldList $fields
     * @return void
     */
    public function updateCMSFields(FieldList $fields): void
    {
        $summaryHTMLEditor = $fields->dataFieldByName('Summary');
        $summaryHTMLEditor->addExtraClass('stacked');
        $fields->insertBefore('CustomSummary', $summaryHTMLEditor);
        $fields->removeByName('CustomSummary');
    }
}
