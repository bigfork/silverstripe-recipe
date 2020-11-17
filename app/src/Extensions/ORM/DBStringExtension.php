<?php

namespace App\Extensions\ORM;

use SilverStripe\Core\Extension;

class DBStringExtension extends Extension
{
    /**
     * @return string
     */
    public function TelephoneHref(): string
    {
        return preg_replace(['/^0/', '/\s+/'], ['+44', ''], $this->owner->value);
    }
}
