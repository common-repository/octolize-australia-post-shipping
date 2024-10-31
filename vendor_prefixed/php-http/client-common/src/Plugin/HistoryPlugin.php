<?php

declare (strict_types=1);
namespace OctolizeShippingAustraliaPostVendor\Http\Client\Common\Plugin;

use OctolizeShippingAustraliaPostVendor\Http\Client\Common\Plugin;
use OctolizeShippingAustraliaPostVendor\Http\Promise\Promise;
use OctolizeShippingAustraliaPostVendor\Psr\Http\Client\ClientExceptionInterface;
use OctolizeShippingAustraliaPostVendor\Psr\Http\Message\RequestInterface;
use OctolizeShippingAustraliaPostVendor\Psr\Http\Message\ResponseInterface;
/**
 * Record HTTP calls.
 *
 * @author Joel Wurtz <joel.wurtz@gmail.com>
 */
final class HistoryPlugin implements Plugin
{
    /**
     * Journal use to store request / responses / exception.
     *
     * @var Journal
     */
    private $journal;
    public function __construct(Journal $journal)
    {
        $this->journal = $journal;
    }
    public function handleRequest(RequestInterface $request, callable $next, callable $first): Promise
    {
        $journal = $this->journal;
        return $next($request)->then(function (ResponseInterface $response) use ($request, $journal) {
            $journal->addSuccess($request, $response);
            return $response;
        }, function (ClientExceptionInterface $exception) use ($request, $journal) {
            $journal->addFailure($request, $exception);
            throw $exception;
        });
    }
}
