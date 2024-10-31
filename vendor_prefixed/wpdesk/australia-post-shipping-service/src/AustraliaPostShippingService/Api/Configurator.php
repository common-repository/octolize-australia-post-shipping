<?php

namespace OctolizeShippingAustraliaPostVendor\WPDesk\AustraliaPostShippingService\Api;

use OctolizeShippingAustraliaPostVendor\Http\Client\Common\Plugin\AddHostPlugin;
use OctolizeShippingAustraliaPostVendor\Http\Client\Common\Plugin\HeaderDefaultsPlugin;
use OctolizeShippingAustraliaPostVendor\Http\Client\Common\PluginClient;
use OctolizeShippingAustraliaPostVendor\Http\Discovery\HttpClientDiscovery;
use OctolizeShippingAustraliaPostVendor\Http\Discovery\UriFactoryDiscovery;
use OctolizeShippingAustraliaPostVendor\Psr\Log\LoggerAwareInterface;
use OctolizeShippingAustraliaPostVendor\Psr\Log\LoggerAwareTrait;
use OctolizeShippingAustraliaPostVendor\WPDesk\AustraliaPostShippingService\Http\LoggerPlugin;
/**
 * Australia Post API Configuration.
 */
class Configurator extends \OctolizeShippingAustraliaPostVendor\Fontis\Auspost\HttpClient\Configurator implements LoggerAwareInterface
{
    use LoggerAwareTrait;
    public function createHttpClient()
    {
        $client = HttpClientDiscovery::find();
        $plugins = [new AddHostPlugin(UriFactoryDiscovery::find()->createUri($this->getEndpoint())), new HeaderDefaultsPlugin(['auth-key' => $this->getApiKey()]), new LoggerPlugin($this->logger)];
        return new PluginClient($client, $plugins);
    }
}
