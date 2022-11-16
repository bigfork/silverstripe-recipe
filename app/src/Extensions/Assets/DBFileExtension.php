<?php

namespace App\Extensions\Assets;

use SilverStripe\Assets\Storage\AssetStore;
use SilverStripe\Core\Extension;

class DBFileExtension extends Extension
{
    public function updateURL(&$url)
    {
        $visibility = $this->owner->getVisibility();
        if ($visibility === AssetStore::VISIBILITY_PROTECTED) {
            $url .= '?';
        }
    }
}
