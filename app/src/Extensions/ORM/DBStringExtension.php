<?php

namespace App\Extensions\ORM;

use SilverStripe\Core\Extension;

class DBStringExtension extends Extension
{
    public function TelephoneHref(): string
    {
        return preg_replace(['/^0/', '/\s+/'], ['+44', ''], $this->owner->value);
    }

    public function SnakeCase(): string
    {
        $input = $this->owner->value ?? '';
        // Replace any non-letter and non-number characters with an underscore
        $input = preg_replace('/[^a-zA-Z0-9]+/', '_', $input);
        // Convert camelCase to snake_case
        $input = preg_replace('/([a-z])([A-Z])/', '$1_$2', $input);
        // Convert the entire string to lowercase
        $input = strtolower($input);
        // Trim any leading or trailing underscores
        $input = trim($input, '_');

        return $input;
    }
}
