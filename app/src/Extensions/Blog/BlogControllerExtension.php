<?php

namespace App\Extensions\Blog;

use SilverStripe\Core\Extension;

class BlogControllerExtension extends Extension
{
    public function updateMetaTitle(&$title): void
    {
        if (!$this->owner->data()->MetaTitle) {
            return;
        }

        $title = $this->owner->data()->MetaTitle;
        $filter = $this->owner->getFilterDescription();
        if ($filter) {
            $title = sprintf('%s - %s', $title, $filter);
        }
    }
}
