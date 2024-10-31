<?php

namespace OctolizeShippingAustraliaPostVendor\WPDesk\Logger;

use OctolizeShippingAustraliaPostVendor\Monolog\Logger;
/*
 * @package WPDesk\Logger
 */
interface LoggerFactory
{
    /**
     * Returns created Logger
     *
     * @param string $name
     *
     * @return Logger
     */
    public function getLogger($name);
}
