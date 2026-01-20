<?php

namespace App\Extensions\Blog;

use SilverStripe\Blog\Model\BlogController;
use SilverStripe\Control\Controller;
use SilverStripe\Control\Director;
use SilverStripe\Core\Extension;

class BlogExtension extends Extension
{
    public function updateMetaComponents(array &$tags): void
    {
        if (!Controller::has_curr()) {
            return;
        }

        /** @var BlogController $controller */
        $controller = Controller::curr();
        $pages = $controller->PaginatedList();
        if (!$pages->getTotalItems()) {
            return;
        }

        // Filtered views are noindexed, as they're just duplicating content from the main blog
        if (
            $controller->getCurrentCategory()
            || $controller->getCurrentTag()
            || $controller->getCurrentProfile()
            || $controller->getArchiveDate()
        ) {
            $tags['noindex'] = [
                'tag' => 'meta',
                'attributes' => [
                    'name' => 'robots',
                    'content' => 'noindex',
                ],
            ];
            return;
        }

        $getVar = $pages->getPaginationGetVar();
        $canonicalURL = $this->owner->Link();
        if ($start = $pages->getPageStart()) {
            $canonicalURL = $this->owner->Link("?{$getVar}={$start}");
        }

        $tags['canonical'] = [
            'tag' => 'link',
            'attributes' => [
                'rel' => 'canonical',
                'href' => Director::absoluteURL($canonicalURL),
            ]
        ];

        if ($pages->NextLink()) {
            $start = $pages->getPageStart() + $pages->getPageLength();
            $canonicalNext = $this->owner->Link("?{$getVar}={$start}");
            $tags['next'] = [
                'tag' => 'link',
                'attributes' => [
                    'rel' => 'next',
                    'href' => Director::absoluteURL($canonicalNext)
                ]
            ];
        }

        if ($pages->CurrentPage() !== 1) {
            $canonicalPrev = $this->owner->Link();
            if ($pages->PrevLink() && $pages->CurrentPage() > 2) {
                $start = $pages->getPageStart() - $pages->getPageLength();
                $canonicalPrev = $this->owner->Link("?{$getVar}={$start}");
            }

            $tags['prev'] = [
                'tag' => 'link',
                'attributes' => [
                    'rel' => 'prev',
                    'href' => Director::absoluteURL($canonicalPrev)
                ]
            ];
        }
    }
}
