<?php

namespace App\Extensions\Blog;

use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\Core\Extension;
use SilverStripe\Forms\FieldList;

class BlogPostExtension extends Extension
{
    public function updateCMSFields(FieldList $fields): void
    {
        // Move summary out of ToggleCompositeField
        $summaryHTMLEditor = $fields->dataFieldByName('Summary');
        $summaryHTMLEditor->setTitle('Post summary');
        $fields->insertBefore('CustomSummary', $summaryHTMLEditor);
        $fields->removeByName(['CustomSummary']);

        /** @var UploadField $image */
        $image = $fields->dataFieldByName('FeaturedImage');
        $image->setFolderName('blog');
    }
}
