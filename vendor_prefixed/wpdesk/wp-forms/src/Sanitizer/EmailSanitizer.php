<?php

namespace OctolizeShippingAustraliaPostVendor\WPDesk\Forms\Sanitizer;

use OctolizeShippingAustraliaPostVendor\WPDesk\Forms\Sanitizer;
class EmailSanitizer implements Sanitizer
{
    public function sanitize($value): string
    {
        return sanitize_email($value);
    }
}
