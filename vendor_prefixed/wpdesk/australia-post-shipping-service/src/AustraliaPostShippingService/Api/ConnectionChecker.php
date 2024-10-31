<?php

namespace OctolizeShippingAustraliaPostVendor\WPDesk\AustraliaPostShippingService\Api;

use OctolizeShippingAustraliaPostVendor\Fontis\Auspost\Api\Postage\Domestic\Letter\Services\GetServicesParams;
use OctolizeShippingAustraliaPostVendor\Fontis\Auspost\Api\Postage\Domestic\Parcel\Cost\CalculationParams;
use OctolizeShippingAustraliaPostVendor\Fontis\Auspost\Exception\EndpointServiceError;
use OctolizeShippingAustraliaPostVendor\Fontis\Auspost\Model\Postage\Enum\ServiceCode;
use http\Exception\RuntimeException;
use OctolizeShippingAustraliaPostVendor\Psr\Log\LoggerInterface;
use OctolizeShippingAustraliaPostVendor\WPDesk\AbstractShipping\Settings\SettingsValues;
use OctolizeShippingAustraliaPostVendor\WPDesk\AbstractShipping\Settings\SettingsValuesAsArray;
use OctolizeShippingAustraliaPostVendor\WPDesk\AustraliaPostShippingService\AustraliaPostSettingsDefinition;
use OctolizeShippingAustraliaPostVendor\WPDesk\AustraliaPostShippingService\AustraliaPostShippingService;
use OctolizeShippingAustraliaPostVendor\WPDesk\AustraliaPostShippingService\Exception\ApiResponseException;
/**
 * Can check connection.
 */
class ConnectionChecker
{
    /**
     * Settings.
     *
     * @var SettingsValuesAsArray
     */
    private $settings;
    /**
     * Logger.
     *
     * @var LoggerInterface
     */
    private $logger;
    /**
     * ConnectionChecker constructor.
     *
     * @param SettingsValues $settings .
     * @param LoggerInterface $logger .
     */
    public function __construct(SettingsValues $settings, LoggerInterface $logger)
    {
        $this->settings = $settings;
        $this->logger = $logger;
    }
    /**
     * Pings API.
     *
     * @throws \Exception .
     */
    public function check_connection()
    {
        $this->logger->debug('Connection checker', ['source' => 'australiapost']);
        $auspost = Auspost::create_with_logger($this->settings->get_value(AustraliaPostSettingsDefinition::API_KEY, AustraliaPostSettingsDefinition::DEFAULT_API_KEY), $this->logger);
        try {
            $result = $auspost->postage()->calculateDomesticParcelPostage(new CalculationParams(3000, 3011, 10, 10, 10, 10, ServiceCode::AUS_PARCEL_REGULAR));
            $this->logger->debug('Connection success', ['source' => 'australiapost', 'rates' => $result]);
        } catch (EndpointServiceError $ese) {
            $message = $auspost->getMessageFromException($ese);
            $this->logger->debug(' Connection checker error', ['source' => 'australiapost', 'error' => $message]);
            throw new ApiResponseException($message);
        } catch (\Exception $e) {
            $this->logger->debug(' Connection checker error', ['source' => 'australiapost', 'error' => $e->getMessage()]);
            throw $e;
        }
    }
}
