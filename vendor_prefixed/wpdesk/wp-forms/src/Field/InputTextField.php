<?php

namespace OctolizeShippingAustraliaPostVendor\WPDesk\Forms\Field;

use OctolizeShippingAustraliaPostVendor\WPDesk\Forms\Sanitizer;
use OctolizeShippingAustraliaPostVendor\WPDesk\Forms\Sanitizer\TextFieldSanitizer;
class InputTextField extends BasicField
{
    public function get_sanitizer(): Sanitizer
    {
        return new TextFieldSanitizer();
    }
    public function get_template_name(): string
    {
        return 'input-text';
    }
}
