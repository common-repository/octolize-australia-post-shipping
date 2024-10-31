<?php

namespace OctolizeShippingAustraliaPostVendor\WPDesk\AustraliaPostShippingService\Api;

use OctolizeShippingAustraliaPostVendor\Fontis\Auspost\Api\Postage\Domestic\Parcel\Cost\CalculationResponse;
use OctolizeShippingAustraliaPostVendor\Psr\Log\LoggerAwareInterface;
use OctolizeShippingAustraliaPostVendor\Psr\Log\LoggerAwareTrait;
use OctolizeShippingAustraliaPostVendor\Psr\Log\LoggerInterface;
use OctolizeShippingAustraliaPostVendor\WPDesk\AbstractShipping\Exception\RateException;
use OctolizeShippingAustraliaPostVendor\WPDesk\AbstractShipping\Exception\UnitConversionException;
use OctolizeShippingAustraliaPostVendor\WPDesk\AbstractShipping\Settings\SettingsValues;
use OctolizeShippingAustraliaPostVendor\WPDesk\AbstractShipping\Shipment\Dimensions;
use OctolizeShippingAustraliaPostVendor\WPDesk\AbstractShipping\Shipment\Package;
use OctolizeShippingAustraliaPostVendor\WPDesk\AbstractShipping\Shipment\Shipment;
use OctolizeShippingAustraliaPostVendor\WPDesk\AbstractShipping\UnitConversion\UniversalDimension;
use OctolizeShippingAustraliaPostVendor\WPDesk\AbstractShipping\UnitConversion\UniversalWeight;
use OctolizeShippingAustraliaPostVendor\WPDesk\AustraliaPostShippingService\AustraliaPostSettingsDefinition;
use OctolizeShippingAustraliaPostVendor\WPDesk\AustraliaPostShippingService\Exception\TooManyPackagesException;
use OctolizeShippingAustraliaPostVendor\WPDesk\WooCommerceShipping\ShopSettings;
/**
 * Can build request to Australia Post API.
 */
abstract class AustraliaPostRateRequestBuilder implements LoggerAwareInterface
{
    use LoggerAwareTrait;
    /**
     * @var Auspost
     */
    protected $auspost;
    /**
     * WooCommerce shipment.
     *
     * @var Shipment
     */
    protected $shipment;
    /**
     * Settings values.
     *
     * @var SettingsValues
     */
    protected $settings;
    /**
     * Shop settings.
     *
     * @var ShopSettings
     */
    protected $shop_settings;
    /**
     * @var array
     */
    protected $services;
    /**
     * CabadaPostRateRequestBuilder constructor.
     *
     * @param Auspost $auspost .
     * @param SettingsValues $settings Settings.
     * @param Shipment $shipment Shipment.
     * @param ShopSettings $helper Helper.
     * @param array $services Services.
     * @param LoggerInterface $logger Logger.
     */
    public function __construct(Auspost $auspost, SettingsValues $settings, Shipment $shipment, ShopSettings $helper, array $services, LoggerInterface $logger)
    {
        $this->auspost = $auspost;
        $this->settings = $settings;
        $this->shipment = $shipment;
        $this->shop_settings = $helper;
        $this->services = $services;
        $this->setLogger($logger);
    }
    /**
     * Calculate package weight.
     *
     * @param Package $shipment_package .
     * @param string $weight_unit .
     *
     * @return float
     * @throws UnitConversionException Weight exception.
     */
    protected function calculate_package_weight(Package $shipment_package, $weight_unit): float
    {
        $package_weight = 0.0;
        foreach ($shipment_package->items as $item) {
            $item_weight = (new UniversalWeight($item->weight->weight, $item->weight->weight_unit))->as_unit_rounded($weight_unit);
            $package_weight += $item_weight;
        }
        if ($package_weight === 0.0) {
            $package_weight = (float) $this->settings->get_value(AustraliaPostSettingsDefinition::PACKAGE_WEIGHT);
        }
        return $package_weight;
    }
    /**
     * @param float $value
     * @param string $from_unit
     * @param string $to_unit
     *
     * @return float
     * @throws UnitConversionException
     */
    protected function calculate_dimension($value, $from_unit, $to_unit)
    {
        return (new UniversalDimension($value, $from_unit))->as_unit_rounded($to_unit);
    }
    /**
     * @throws TooManyPackagesException|RateException
     */
    protected function check_packages()
    {
        if (count($this->shipment->packages) > 1) {
            throw new TooManyPackagesException(__('Too many packages in shipment!', 'canada-post-shipping-service'));
        }
        if (count($this->shipment->packages) === 0) {
            throw new RateException(__('The shipment does not include any package!', 'canada-post-shipping-service'));
        }
    }
    /**
     * @param Package $package
     *
     * @return Dimensions
     */
    protected function prepare_dimensions(Package $package)
    {
        $dimensions = new Dimensions();
        $dimensions->dimensions_unit = Dimensions::DIMENSION_UNIT_CM;
        if (isset($package->dimensions)) {
            $dimensions->length = $this->calculate_dimension($package->dimensions->length, $package->dimensions->dimensions_unit, Dimensions::DIMENSION_UNIT_CM);
            $dimensions->width = $this->calculate_dimension($package->dimensions->width, $package->dimensions->dimensions_unit, Dimensions::DIMENSION_UNIT_CM);
            $dimensions->height = $this->calculate_dimension($package->dimensions->height, $package->dimensions->dimensions_unit, Dimensions::DIMENSION_UNIT_CM);
        } else {
            $dimensions->length = (float) $this->settings->get_value(AustraliaPostSettingsDefinition::PACKAGE_LENGTH);
            $dimensions->width = (float) $this->settings->get_value(AustraliaPostSettingsDefinition::PACKAGE_WIDTH);
            $dimensions->height = (float) $this->settings->get_value(AustraliaPostSettingsDefinition::PACKAGE_HEIGHT);
        }
        return $dimensions;
    }
    /**
     * @return CalculationResponse[]
     */
    abstract public function get_response();
}
