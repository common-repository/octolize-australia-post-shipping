<?php

namespace OctolizeShippingAustraliaPostVendor\WPDesk\Forms\Validator;

use OctolizeShippingAustraliaPostVendor\WPDesk\Forms\Validator;
class NoValidateValidator implements Validator
{
    public function is_valid($value): bool
    {
        return \true;
    }
    public function get_messages(): array
    {
        return [];
    }
}
