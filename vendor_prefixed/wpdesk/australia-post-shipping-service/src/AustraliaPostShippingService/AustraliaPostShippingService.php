<?php

namespace OctolizeShippingAustraliaPostVendor\WPDesk\AustraliaPostShippingService;

use OctolizeShippingAustraliaPostVendor\Fontis\Auspost\Api\Postage\Domestic\Parcel\Cost\CalculationResponse as CalculationResponseDomestic;
use OctolizeShippingAustraliaPostVendor\Fontis\Auspost\Api\Postage\International\Parcel\Cost\CalculationResponse as CalculationResponseInternational;
use OctolizeShippingAustraliaPostVendor\Fontis\Auspost\Exception\EndpointServiceError;
use OctolizeShippingAustraliaPostVendor\Psr\Log\LoggerInterface;
use OctolizeShippingAustraliaPostVendor\WPDesk\AbstractShipping\Exception\InvalidSettingsException;
use OctolizeShippingAustraliaPostVendor\WPDesk\AbstractShipping\Exception\RateException;
use OctolizeShippingAustraliaPostVendor\WPDesk\AbstractShipping\Exception\UnitConversionException;
use OctolizeShippingAustraliaPostVendor\WPDesk\AbstractShipping\Rate\ShipmentRating;
use OctolizeShippingAustraliaPostVendor\WPDesk\AbstractShipping\Rate\ShipmentRatingImplementation;
use OctolizeShippingAustraliaPostVendor\WPDesk\AbstractShipping\Rate\SingleRate;
use OctolizeShippingAustraliaPostVendor\WPDesk\AbstractShipping\Settings\SettingsValues;
use OctolizeShippingAustraliaPostVendor\WPDesk\AbstractShipping\Shipment\Shipment;
use OctolizeShippingAustraliaPostVendor\WPDesk\AbstractShipping\ShippingService;
use OctolizeShippingAustraliaPostVendor\WPDesk\AbstractShipping\ShippingServiceCapability\CanRate;
use OctolizeShippingAustraliaPostVendor\WPDesk\AbstractShipping\ShippingServiceCapability\CanTestSettings;
use OctolizeShippingAustraliaPostVendor\WPDesk\AbstractShipping\ShippingServiceCapability\HasSettings;
use OctolizeShippingAustraliaPostVendor\WPDesk\AustraliaPostShippingService\Api\Auspost;
use OctolizeShippingAustraliaPostVendor\WPDesk\AustraliaPostShippingService\Api\AustraliaPostDomesticRateReplyInterpretation;
use OctolizeShippingAustraliaPostVendor\WPDesk\AustraliaPostShippingService\Api\AustraliaPostDomesticRateRequestBuilder;
use OctolizeShippingAustraliaPostVendor\WPDesk\AustraliaPostShippingService\Api\AustraliaPostInternationalRateReplyInterpretation;
use OctolizeShippingAustraliaPostVendor\WPDesk\AustraliaPostShippingService\Api\AustraliaPostInternationalRateRequestBuilder;
use OctolizeShippingAustraliaPostVendor\WPDesk\AustraliaPostShippingService\Api\ConnectionChecker;
use OctolizeShippingAustraliaPostVendor\WPDesk\AustraliaPostShippingService\Api\AustraliaPostRateReplyInterpretation;
use OctolizeShippingAustraliaPostVendor\WPDesk\AustraliaPostShippingService\Api\AustraliaPostRateRequestBuilder;
use OctolizeShippingAustraliaPostVendor\WPDesk\AustraliaPostShippingService\Exception\ApiResponseException;
use OctolizeShippingAustraliaPostVendor\WPDesk\AustraliaPostShippingService\Exception\CurrencySwitcherException;
use OctolizeShippingAustraliaPostVendor\WPDesk\WooCommerceShipping\ShopSettings;
/**
 * Australia Post main shipping class injected into WooCommerce shipping method.
 */
class AustraliaPostShippingService extends ShippingService implements HasSettings, CanRate, CanTestSettings
{
    const DOMESTIC = 'domestic';
    const INTERNATIONAL = 'international';
    /** Logger.
     *
     * @var LoggerInterface
     */
    private $logger;
    /** Shipping method helper.
     *
     * @var ShopSettings
     */
    private $shop_settings;
    /**
     * Origin country.
     *
     * @var string
     */
    private $origin_country;
    const UNIQUE_ID = 'octolize_australia_post_shipping';
    /**
     * AustraliaPostShippingService constructor.
     *
     * @param LoggerInterface $logger Logger.
     * @param ShopSettings $shop_settings Helper.
     * @param string $origin_country Origin country.
     */
    public function __construct(LoggerInterface $logger, ShopSettings $shop_settings, string $origin_country)
    {
        $this->logger = $logger;
        $this->shop_settings = $shop_settings;
        $this->origin_country = $origin_country;
    }
    /**
     * Set logger.
     *
     * @param LoggerInterface $logger Logger.
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
    /**
     * .
     *
     * @return LoggerInterface
     */
    public function get_logger(): LoggerInterface
    {
        return $this->logger;
    }
    /**
     * .
     *
     * @return ShopSettings
     */
    public function get_shop_settings(): ShopSettings
    {
        return $this->shop_settings;
    }
    /**
     * Is standard rate enabled?
     *
     * @param SettingsValues $settings .
     *
     * @return bool
     */
    public function is_rate_enabled(SettingsValues $settings): bool
    {
        return \true;
    }
    /**
     * Rate shipment.
     *
     * @param SettingsValues $settings Settings.
     * @param Shipment $shipment Shipment.
     *
     * @return ShipmentRating
     * @throws InvalidSettingsException InvalidSettingsException.
     * @throws RateException RateException.
     * @throws UnitConversionException Weight exception.
     */
    public function rate_shipment(SettingsValues $settings, Shipment $shipment): ShipmentRating
    {
        if (!$this->get_settings_definition()->validate_settings($settings)) {
            throw new InvalidSettingsException();
        }
        $this->verify_currency($this->shop_settings->get_default_currency(), $this->shop_settings->get_currency());
        $auspost = Auspost::create_with_logger($settings->get_value(AustraliaPostSettingsDefinition::API_KEY, AustraliaPostSettingsDefinition::DEFAULT_API_KEY), $this->logger);
        try {
            if ($this->is_domestic_shipment($shipment)) {
                $request_builder = $this->create_domestic_rate_request_builder($auspost, $settings, $shipment, $this->shop_settings);
                $reply = $this->create_domestic_reply_interpretation($request_builder->get_response(), $this->shop_settings, $settings);
            } else {
                $request_builder = $this->create_international_rate_request_builder($auspost, $settings, $shipment, $this->shop_settings);
                $reply = $this->create_international_reply_interpretation($request_builder->get_response(), $this->shop_settings, $settings);
            }
        } catch (EndpointServiceError $ese) {
            $message = $auspost->getMessageFromException($ese);
            $this->logger->debug(' Connection checker error', ['source' => 'australiapost', 'error' => $message]);
            throw new ApiResponseException($message);
        }
        if ($reply->has_reply_warning()) {
            $this->logger->info($reply->get_reply_message());
        }
        if (!$reply->has_reply_error()) {
            $rates = $this->filter_service_rates($settings, $reply->get_rates());
        }
        if ('yes' === $settings->get_value(AustraliaPostSettingsDefinition::REMOVE_GST, 'no')) {
            $rates = $this->remove_gst_from_rates($rates);
        }
        return new ShipmentRatingImplementation($rates);
    }
    /**
     * @param SingleRate[] $rates
     *
     * @return SingleRatep[
     */
    private function remove_gst_from_rates(array $rates)
    {
        foreach ($rates as $key => $rate) {
            $rate->total_charge->amount = $rate->total_charge->amount / 1.1;
            $rates[$key] = $rate;
        }
        return $rates;
    }
    private function is_domestic_shipment(Shipment $shipment)
    {
        return $shipment->ship_to->address->country_code === 'AU';
    }
    /**
     * @param string $xml_string
     *
     * @return string
     */
    private function pretty_print_xml($xml_string): string
    {
        $xml = new \DOMDocument();
        $xml->preserveWhiteSpace = \false;
        $xml->formatOutput = \true;
        $xml->loadXML($xml_string);
        return $xml->saveXML();
    }
    /**
     * Create reply interpretation.
     *
     * @param CalculationResponseDomestic[] $response
     * @param ShopSettings $shop_settings .
     * @param SettingsValues $settings .
     *
     * @return AustraliaPostRateReplyInterpretation
     */
    protected function create_domestic_reply_interpretation($response, $shop_settings, $settings): AustraliaPostRateReplyInterpretation
    {
        return new AustraliaPostDomesticRateReplyInterpretation($response, $shop_settings->is_tax_enabled());
    }
    /**
     * Create reply interpretation.
     *
     * @param CalculationResponseInternational[] $response .
     * @param ShopSettings $shop_settings .
     * @param SettingsValues $settings .
     *
     * @return AustraliaPostRateReplyInterpretation
     */
    protected function create_international_reply_interpretation($response, $shop_settings, $settings): AustraliaPostRateReplyInterpretation
    {
        return new AustraliaPostInternationalRateReplyInterpretation($response, $shop_settings->is_tax_enabled());
    }
    /**
     * Create rate request builder.
     *
     * @param Auspost $auspost .
     * @param SettingsValues $settings .
     * @param Shipment $shipment .
     * @param ShopSettings $shop_settings .
     *
     * @return AustraliaPostRateRequestBuilder
     */
    protected function create_domestic_rate_request_builder(Auspost $auspost, SettingsValues $settings, Shipment $shipment, ShopSettings $shop_settings): AustraliaPostRateRequestBuilder
    {
        return new AustraliaPostDomesticRateRequestBuilder($auspost, $settings, $shipment, $shop_settings, $this->prepare_services($settings, self::DOMESTIC), $this->logger);
    }
    /**
     * Create rate request builder.
     *
     * @param Auspost $auspost .
     * @param SettingsValues $settings .
     * @param Shipment $shipment .
     * @param ShopSettings $shop_settings .
     *
     * @return AustraliaPostRateRequestBuilder
     */
    protected function create_international_rate_request_builder(Auspost $auspost, SettingsValues $settings, Shipment $shipment, ShopSettings $shop_settings): AustraliaPostRateRequestBuilder
    {
        return new AustraliaPostInternationalRateRequestBuilder($auspost, $settings, $shipment, $shop_settings, $this->prepare_services($settings, self::INTERNATIONAL), $this->logger);
    }
    /**
     * @param SettingsValues $settings
     * @param string $type
     * @return array
     */
    private function prepare_services(SettingsValues $settings, $type)
    {
        $services = [];
        $all_services = $this->get_services($type);
        if ($this->is_custom_services_enable($settings)) {
            $services_settings = $this->get_services_settings($settings);
            foreach ($services_settings as $services_setting) {
                if (isset($services_setting['enabled'], $all_services[$services_setting['enabled']])) {
                    $services[$services_setting['enabled']] = $services_setting['name'];
                }
            }
        } else {
            $services = $all_services;
        }
        return $services;
    }
    /**
     * Verify currency.
     *
     * @param string $default_shop_currency .
     * @param string $currency .
     *
     * @throws CurrencySwitcherException .
     */
    protected function verify_currency(string $default_shop_currency, string $currency)
    {
        if ('AUD' !== $currency) {
            throw new CurrencySwitcherException();
        }
    }
    /**
     * Get settings
     *
     * @return AustraliaPostSettingsDefinition
     */
    public function get_settings_definition(): AustraliaPostSettingsDefinition
    {
        return new AustraliaPostSettingsDefinition($this->shop_settings);
    }
    /**
     * Get unique ID.
     *
     * @return string
     */
    public function get_unique_id(): string
    {
        return self::UNIQUE_ID;
    }
    /**
     * Get name.
     *
     * @return string
     */
    public function get_name(): string
    {
        return __('Australia Post Live Rates', 'octolize-australia-post-shipping');
    }
    /**
     * Get description.
     *
     * @return string
     */
    public function get_description(): string
    {
        return __('Australia Post integration', 'octolize-australia-post-shipping');
    }
    /**
     * Pings API.
     * Returns empty string on success or error message on failure.
     *
     * @param SettingsValues $settings .
     * @param LoggerInterface $logger .
     *
     * @return string
     */
    public function check_connection(SettingsValues $settings, LoggerInterface $logger): string
    {
        try {
            $connection_checker = new ConnectionChecker($settings, $logger);
            $connection_checker->check_connection();
            return '';
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
    /**
     * Returns field ID after which API Status field should be added.
     *
     * @return string
     */
    public function get_field_before_api_status_field(): string
    {
        return AustraliaPostSettingsDefinition::DEBUG_MODE;
    }
    /**
     * Filter&change rates according to settings.
     *
     * @param SettingsValues $settings Settings.
     * @param SingleRate[] $australia_post_rates Response.
     *
     * @return SingleRate[]
     */
    private function filter_service_rates(SettingsValues $settings, array $australia_post_rates): array
    {
        $rates = [];
        if (!empty($australia_post_rates)) {
            $all_services = $this->get_services();
            $services_settings = $this->get_services_settings($settings);
            if ($this->is_custom_services_enable($settings)) {
                foreach ($australia_post_rates as $service) {
                    if (isset($service->service_type, $services_settings[$service->service_type]) && !empty($services_settings[$service->service_type]['enabled'])) {
                        $service->service_name = $services_settings[$service->service_type]['name'];
                        $rates[$service->service_type] = $service;
                    }
                }
                $rates = $this->sort_services($rates, $services_settings);
            } else {
                foreach ($australia_post_rates as $service) {
                    if (isset($service->service_type, $all_services[$service->service_type])) {
                        $service->service_name = $all_services[$service->service_type];
                        $rates[$service->service_type] = $service;
                    }
                }
            }
        }
        return $rates;
    }
    /**
     * @param string $type
     *
     * @return array
     */
    private function get_services($type = 'all'): array
    {
        $australia_post_services = new AustraliaPostServices();
        if ($type === self::DOMESTIC) {
            return $australia_post_services->get_services_domestic_au();
        }
        if ($type === self::INTERNATIONAL) {
            return $australia_post_services->get_services_international();
        }
        return $australia_post_services->get_all_services();
    }
    /**
     * @param SettingsValues $settings Settings.
     * @param bool $is_domestic Domestic rates.
     *
     * @return array
     */
    private function get_services_settings(SettingsValues $settings): array
    {
        $services_settings = $settings->get_value(AustraliaPostSettingsDefinition::SERVICES, []);
        return is_array($services_settings) ? $services_settings : [];
    }
    /**
     * Sort rates according to order set in admin settings.
     *
     * @param SingleRate[] $rates Rates.
     * @param array $option_services Saved services to settings.
     *
     * @return SingleRate[]
     */
    private function sort_services(array $rates, array $option_services): array
    {
        if (!empty($option_services)) {
            $services = [];
            foreach ($option_services as $service_code => $service_name) {
                if (isset($rates[$service_code])) {
                    $services[] = $rates[$service_code];
                }
            }
            return $services;
        }
        return $rates;
    }
    /**
     * Are customs service settings enabled.
     *
     * @param SettingsValues $settings Values.
     *
     * @return bool
     */
    private function is_custom_services_enable(SettingsValues $settings): bool
    {
        return $settings->has_value(AustraliaPostSettingsDefinition::CUSTOM_SERVICES) && 'yes' === $settings->get_value(AustraliaPostSettingsDefinition::CUSTOM_SERVICES);
    }
}
