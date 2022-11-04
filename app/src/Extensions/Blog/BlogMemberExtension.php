<?php

namespace App\Extensions\Blog;

use SilverStripe\Core\Extension;
use SilverStripe\Forms\FieldList;

class BlogMemberExtension extends Extension
{
    public function updateCMSFields(FieldList $fields): void
    {
        $fields->removeByName([
            'BlogPosts',
            'BlogProfileSummary',
            'BlogProfileImage'
        ]);
    }
}
