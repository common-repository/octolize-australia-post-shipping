<?php

/**
 * Ajax status handler.
 *
 * @package WPDesk\WooCommerceShipping\AustraliaPost
 */
namespace OctolizeShippingAustraliaPostVendor\WPDesk\WooCommerceShipping\AustraliaPost;

use OctolizeShippingAustraliaPostVendor\WPDesk\AustraliaPostShippingService\Api\ConnectionChecker;
use OctolizeShippingAustraliaPostVendor\WPDesk\WooCommerceShipping\CustomFields\ApiStatus\FieldApiStatusAjax;
/**
 * Can handle api status ajax request.
 */
class AustraliaPostFieldApiStatusAjax extends FieldApiStatusAjax
{
    /**
     * Check connection error.
     *
     * @return string|false
     */
    protected function check_connection_error()
    {
        try {
            $this->ping();
            return \false;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
    /**
     * Ping api.
     *
     * @throws \Exception
     */
    private function ping()
    {
        $connection_checker = new ConnectionChecker($this->get_settings(), $this->get_logger());
        $connection_checker->check_connection();
    }
}
