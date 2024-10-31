<?php

declare (strict_types=1);
namespace OctolizeShippingAustraliaPostVendor\Http\Client\Common;

use OctolizeShippingAustraliaPostVendor\Http\Client\HttpAsyncClient;
use OctolizeShippingAustraliaPostVendor\Psr\Http\Message\RequestInterface;
/**
 * Decorates an HTTP Async Client.
 *
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 */
trait HttpAsyncClientDecorator
{
    /**
     * @var HttpAsyncClient
     */
    protected $httpAsyncClient;
    /**
     * @see HttpAsyncClient::sendAsyncRequest
     */
    public function sendAsyncRequest(RequestInterface $request)
    {
        return $this->httpAsyncClient->sendAsyncRequest($request);
    }
}
