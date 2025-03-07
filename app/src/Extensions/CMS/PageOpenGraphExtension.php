<?php

namespace App\Extensions\CMS;

use SilverStripe\Assets\Image;
use SilverStripe\Assets\Storage\DBFile;
use SilverStripe\Blog\Model\BlogPost;
use SilverStripe\Core\Extension;

class PageOpenGraphExtension extends Extension
{
    public function getOGTitle(): string
    {
        return $this->owner->OGTitleCustom
            ?: $this->owner->MetaTitle
                ?: $this->owner->Title
                    ?: $this->owner->getDefaultOGTitle() ?? '';
    }

    public function getOGImage(): DBFile|Image|string
    {
        /** @var Image $customImage */
        $customImage = $this->owner->OGImageCustom();
        if ($customImage->exists()) {
            return $customImage->ScaleMaxWidth(1200);
        }

        if ($this->owner instanceof BlogPost) {
            /** @var Image $featuredImage */
            $featuredImage = $this->owner->FeaturedImage();
            if ($featuredImage->exists()) {
                return $featuredImage->ScaleMaxWidth(1200);
            }
        }

        return $this->owner->getDefaultOGImage() ?: '';
    }
}
