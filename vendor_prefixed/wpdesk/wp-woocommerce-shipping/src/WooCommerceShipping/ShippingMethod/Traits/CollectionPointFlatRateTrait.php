<?php

/**
 * Flat rate trait.
 *
 * @package WPDesk\WooCommerceShipping\ShippingMethod\Traits
 */
namespace OctolizeShippingAustraliaPostVendor\WPDesk\WooCommerceShipping\ShippingMethod\Traits;

use OctolizeShippingAustraliaPostVendor\WPDesk\WooCommerceShipping\ShippingMethod\RateMethod\FlatRateRateMethod\CollectionPointFlatRateRateMethod;
/**
 * Handles flat rate functionality.
 */
trait CollectionPointFlatRateTrait
{
    /**
     * Return flat rate costs.
     *
     * @param \WC_Shipping_Method $shipping_method .
     *
     * @return float
     */
    public function get_flat_rate_cost($shipping_method)
    {
        return floatval($shipping_method->get_option(CollectionPointFlatRateRateMethod::OPTION_FLAT_RATE_COSTS, '0'));
    }
}
