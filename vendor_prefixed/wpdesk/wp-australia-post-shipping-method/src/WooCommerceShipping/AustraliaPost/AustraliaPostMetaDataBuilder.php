<?php

namespace OctolizeShippingAustraliaPostVendor\WPDesk\WooCommerceShipping\AustraliaPost;

use OctolizeShippingAustraliaPostVendor\WPDesk\AbstractShipping\Rate\SingleRate;
use OctolizeShippingAustraliaPostVendor\WPDesk\AbstractShipping\Shipment\Shipment;
use OctolizeShippingAustraliaPostVendor\WPDesk\WooCommerceShipping\ShippingBuilder\WooCommerceShippingMetaDataBuilder;
/**
 * Can build Canad Post meta data.
 */
class AustraliaPostMetaDataBuilder extends WooCommerceShippingMetaDataBuilder
{
    const META_AUSTRALIA_POST_SERVICE_CODE = 'australia_post_service_id';
    /**
     * Build meta data for rate.
     *
     * @param SingleRate $rate .
     * @param Shipment $shipment .
     *
     * @return array
     */
    public function build_meta_data_for_rate(SingleRate $rate, Shipment $shipment)
    {
        $meta_data = parent::build_meta_data_for_rate($rate, $shipment);
        $meta_data = $this->add_australia_post_service_code_to_metadata($meta_data, $rate);
        return $meta_data;
    }
    /**
     * Add Australia Post service to metadata.
     *
     * @param array $meta_data
     * @param SingleRate $rate
     *
     * @return array
     */
    private function add_australia_post_service_code_to_metadata(array $meta_data, SingleRate $rate)
    {
        $meta_data[self::META_AUSTRALIA_POST_SERVICE_CODE] = $rate->service_type;
        return $meta_data;
    }
}
