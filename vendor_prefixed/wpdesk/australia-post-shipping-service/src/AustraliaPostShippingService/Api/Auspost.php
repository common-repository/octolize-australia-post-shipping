<?php

namespace OctolizeShippingAustraliaPostVendor\WPDesk\AustraliaPostShippingService\Api;

use OctolizeShippingAustraliaPostVendor\Fontis\Auspost\Exception\EndpointServiceError;
use OctolizeShippingAustraliaPostVendor\Psr\Log\LoggerInterface;
/**
 * Australia Post API client.
 */
class Auspost extends \OctolizeShippingAustraliaPostVendor\Fontis\Auspost\Auspost
{
    /**
     * @param string $apiKey
     * @param string $endpoint
     *
     * @return Auspost
     */
    public static function create_with_logger(string $apiKey, LoggerInterface $logger, string $endpoint = 'https://digitalapi.auspost.com.au'): Auspost
    {
        $configurator = new Configurator($apiKey, $endpoint);
        $configurator->setLogger($logger);
        return new self($configurator);
    }
    /**
     * @param EndpointServiceError $e
     *
     * @return string
     */
    public function getMessageFromException(EndpointServiceError $e)
    {
        $message = json_decode($e->getMessage());
        if ($message instanceof \stdClass && isset($message->error->errorMessage)) {
            return $message->error->errorMessage;
        }
        return $e->getMessage();
    }
}
