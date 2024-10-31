<?php

declare (strict_types=1);
namespace OctolizeShippingAustraliaPostVendor\Http\Client\Common\Plugin;

use OctolizeShippingAustraliaPostVendor\Http\Message\Stream\BufferedStream;
use OctolizeShippingAustraliaPostVendor\Http\Promise\Promise;
use OctolizeShippingAustraliaPostVendor\Psr\Http\Message\RequestInterface;
use OctolizeShippingAustraliaPostVendor\Psr\Http\Message\ResponseInterface;
/**
 * Allow body used in response to be always seekable.
 *
 * @author Joel Wurtz <joel.wurtz@gmail.com>
 */
final class ResponseSeekableBodyPlugin extends SeekableBodyPlugin
{
    public function handleRequest(RequestInterface $request, callable $next, callable $first): Promise
    {
        return $next($request)->then(function (ResponseInterface $response) {
            if ($response->getBody()->isSeekable()) {
                return $response;
            }
            return $response->withBody(new BufferedStream($response->getBody(), $this->useFileBuffer, $this->memoryBufferSize));
        });
    }
}
