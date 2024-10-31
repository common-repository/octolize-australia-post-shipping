<?php

/**
 * Capability: CanRateToCollectionPoint class
 *
 * @package WPDesk\AbstractShipping\ShippingServiceCapability
 */
namespace OctolizeShippingAustraliaPostVendor\WPDesk\AbstractShipping\ShippingServiceCapability;

use OctolizeShippingAustraliaPostVendor\WPDesk\AbstractShipping\CollectionPoints\CollectionPoint;
use OctolizeShippingAustraliaPostVendor\WPDesk\AbstractShipping\Rate\ShipmentRating;
use OctolizeShippingAustraliaPostVendor\WPDesk\AbstractShipping\Settings\SettingsValues;
use OctolizeShippingAustraliaPostVendor\WPDesk\AbstractShipping\Shipment\Shipment;
/**
 * Interface for rate shipment to collection point
 */
interface CanRateToCollectionPoint
{
    /**
     * Rate shipment to collection point.
     *
     * @param SettingsValues  $settings Settings.
     * @param Shipment        $shipment Shipment.
     * @param CollectionPoint $collection_point Collection point.
     *
     * @return ShipmentRating
     */
    public function rate_shipment_to_collection_point(SettingsValues $settings, Shipment $shipment, CollectionPoint $collection_point);
    /**
     * Is rate to collection point enabled?
     *
     * @param SettingsValues $settings
     *
     * @return mixed
     */
    public function is_rate_to_collection_point_enabled(SettingsValues $settings);
}
