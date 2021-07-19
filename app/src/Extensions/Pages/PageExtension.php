<?php

namespace App\Extensions\Pages;

use SilverStripe\Core\Extension;

class PageExtension extends Extension
{
    /**
     * @param array $tags
     */
    public function MetaComponents(array &$tags)
    {
        if (!isset($tags['canonical'])) {
            $tags['canonical'] = [
                'tag' => 'link',
                'attributes' => [
                    'rel' => 'canonical',
                    'href' => $this->owner->AbsoluteLink(),
                ]
            ];
        }
    }
}
