<?php

namespace OctolizeShippingAustraliaPostVendor\WPDesk\Forms\Sanitizer;

use OctolizeShippingAustraliaPostVendor\WPDesk\Forms\Sanitizer;
class NoSanitize implements Sanitizer
{
    public function sanitize($value)
    {
        return $value;
    }
}
