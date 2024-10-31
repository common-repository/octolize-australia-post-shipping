<?php

namespace OctolizeShippingAustraliaPostVendor\WPDesk\Forms\Serializer;

use OctolizeShippingAustraliaPostVendor\WPDesk\Forms\Serializer;
class JsonSerializer implements Serializer
{
    public function serialize($value): string
    {
        return (string) json_encode($value);
    }
    public function unserialize(string $value)
    {
        return json_decode($value, \true);
    }
}
