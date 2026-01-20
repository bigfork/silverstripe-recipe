<?php

namespace App\Extensions\SimpleSEO;

use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\Assets\Image;
use SilverStripe\Control\Director;
use SilverStripe\Core\Extension;
use SilverStripe\Forms\FieldList;

class SimpleSEOExtension extends Extension
{
    const SHARE_IMAGE_DIMENSIONS = [
        'w' => 1600,
        'h' => 900,
    ];

    private static array $has_one = [
        'ShareImage' => Image::class,
    ];

    private static array $owns = [
        'ShareImage',
    ];

    public function updateCMSFields(FieldList $fields): void
    {
        $dimensions = self::SHARE_IMAGE_DIMENSIONS;

        $fields->insertAfter(
            'MetaDescription',
            UploadField::create('ShareImage', 'Social share image')
                ->setFolderName('social-shares')
                ->setAllowedFileCategories('image/supported')
                ->setRightTitle('Shown when sharing a URL on social platforms or an instant messaging app such as Discord or Whatsapp.')
                ->setDescription("Ideal size is {$dimensions['w']}px x {$dimensions['h']}px, image will be focus filled to be fit."),
        );
    }

    public function updateMetaComponents(array &$tags): void
    {
        $owner = $this->getOwner();
        $image = $owner->ShareImage();

        if (!$image->exists()) {
            // Example fallback when paired with the silverstripe/blog module
            if (
                $owner->hasMethod('FeaturedImage')
                && $owner->FeaturedImage()->exists()
            ) {
                $image = $owner->FeaturedImage();
            }
        }

        $image_src = $image->exists()
            ? $image->FocusFill(self::SHARE_IMAGE_DIMENSIONS['w'], self::SHARE_IMAGE_DIMENSIONS['h'])->AbsoluteLink()
            : Director::absoluteURL('social-share.png');
        $image_type = $image->exists()
            ? "image/{$image->getExtension()}"
            : 'image/png';

        $tags['image_src'] = [
            'tag' => 'link',
            'attributes' => [
                'rel' => 'image_src',
                'href' => $image_src,
            ],
        ];

        $tags['og:image'] = [
            'tag' => 'meta',
            'attributes' => [
                'property' => 'og:image',
                'content' => $image_src,
            ],
        ];

        $tags['og:image:type'] = [
            'tag' => 'meta',
            'attributes' => [
                'property' => 'og:image:type',
                'content' => $image_type,
            ],
        ];

        $tags['twitter:image'] = [
            'tag' => 'meta',
            'attributes' => [
                'name' => 'twitter:image',
                'content' => $image_src,
            ],
        ];

        $tags['twitter:card'] = [
            'tag' => 'meta',
            'attributes' => [
                'name' => 'twitter:card',
                'content' => 'summary_large_image',
            ],
        ];
    }
}
