<?php

/**
 * @package WPDesk\WooCommerceShipping\AustraliaPost
 */
namespace OctolizeShippingAustraliaPostVendor\WPDesk\WooCommerceShipping\AustraliaPost;

use OctolizeShippingAustraliaPostVendor\WPDesk\WooCommerceShipping\OrderMetaData\AdminOrderMetaDataDisplay;
use OctolizeShippingAustraliaPostVendor\WPDesk\WooCommerceShipping\OrderMetaData\SingleAdminOrderMetaDataInterpreterImplementation;
use OctolizeShippingAustraliaPostVendor\WPDesk\WooCommerceShipping\ShippingBuilder\WooCommerceShippingMetaDataBuilder;
/**
 * Can hide meta data in order.
 */
class AustraliaPostAdminOrderMetaDataDisplay extends AdminOrderMetaDataDisplay
{
    /**
     * @param string $method_id .
     */
    public function __construct($method_id)
    {
        parent::__construct($method_id);
        $this->add_hidden_order_item_meta_key(WooCommerceShippingMetaDataBuilder::SERVICE_TYPE);
        $this->add_interpreter(new SingleAdminOrderMetaDataInterpreterImplementation(AustraliaPostMetaDataBuilder::META_AUSTRALIA_POST_SERVICE_CODE, __('Australia Post Service Code', 'octolize-australia-post-shipping')));
    }
}
