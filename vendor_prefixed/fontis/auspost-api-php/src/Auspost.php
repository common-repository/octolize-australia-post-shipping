<?php

/**
 * Fontis Australia Post API client library for PHP
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 *
 * @category   Fontis
 * @package    Fontis_Auspost
 * @copyright  Copyright (c) 2019 Fontis Pty. Ltd. (https://www.fontis.com.au)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
declare (strict_types=1);
namespace OctolizeShippingAustraliaPostVendor\Fontis\Auspost;

use OctolizeShippingAustraliaPostVendor\Fontis\Auspost\Api\Postage;
use OctolizeShippingAustraliaPostVendor\Fontis\Auspost\HttpClient\Configurator;
use OctolizeShippingAustraliaPostVendor\Fontis\Auspost\HttpClient\HttpApi;
use OctolizeShippingAustraliaPostVendor\Fontis\Auspost\HttpClient\RequestBuilder;
use OctolizeShippingAustraliaPostVendor\Http\Client\HttpClient;
use OctolizeShippingAustraliaPostVendor\Http\Message\MessageFactory;
class Auspost
{
    /**
     * @var HttpClient
     */
    protected $httpClient;
    /**
     * @var RequestBuilder
     */
    private $requestBuilder;
    /**
     * @var MessageFactory
     */
    protected $messageFactory;
    /**
     * Constructor
     *
     * @param Configurator $configurator
     * @param RequestBuilder|null $requestBuilder
     */
    public function __construct(Configurator $configurator, RequestBuilder $requestBuilder = null)
    {
        $this->httpClient = $configurator->createHttpClient();
        $this->requestBuilder = $requestBuilder ?: new RequestBuilder();
    }
    /**
     * @param string $apiKey
     * @param string $endpoint
     * @return Auspost
     */
    public static function create(string $apiKey, string $endpoint = 'https://digitalapi.auspost.com.au'): self
    {
        $configurator = new Configurator($apiKey, $endpoint);
        return new self($configurator);
    }
    /**
     * @return Postage
     */
    public function postage(): Postage
    {
        $httpClient = new HttpApi($this->httpClient, $this->requestBuilder);
        return new Postage($httpClient);
    }
}
