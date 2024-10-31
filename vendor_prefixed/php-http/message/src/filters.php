<?php

namespace OctolizeShippingAustraliaPostVendor;

// Register chunk filter if not found
if (!\array_key_exists('chunk', \stream_get_filters())) {
    \stream_filter_register('chunk', 'OctolizeShippingAustraliaPostVendor\Http\Message\Encoding\Filter\Chunk');
}
