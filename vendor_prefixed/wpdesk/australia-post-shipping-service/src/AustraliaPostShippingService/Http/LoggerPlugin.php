<?php

namespace OctolizeShippingAustraliaPostVendor\WPDesk\AustraliaPostShippingService\Http;

use OctolizeShippingAustraliaPostVendor\Http\Client\Common\Plugin;
use OctolizeShippingAustraliaPostVendor\Http\Client\Common\Plugin\VersionBridgePlugin;
use OctolizeShippingAustraliaPostVendor\Http\Client\Exception;
use OctolizeShippingAustraliaPostVendor\Http\Message\Formatter;
use OctolizeShippingAustraliaPostVendor\Http\Message\Formatter\SimpleFormatter;
use OctolizeShippingAustraliaPostVendor\Psr\Http\Message\RequestInterface;
use OctolizeShippingAustraliaPostVendor\Psr\Http\Message\ResponseInterface;
use OctolizeShippingAustraliaPostVendor\Psr\Log\LoggerInterface;
/**
 * Log request, response and exception for an HTTP Client.
 *
 * @author Joel Wurtz <joel.wurtz@gmail.com>
 */
final class LoggerPlugin implements Plugin
{
    use VersionBridgePlugin;
    private $logger;
    private $formatter;
    public function __construct(LoggerInterface $logger, Formatter $formatter = null)
    {
        $this->logger = $logger;
        $this->formatter = $formatter ?: new SimpleFormatter();
    }
    protected function doHandleRequest(RequestInterface $request, callable $next, callable $first)
    {
        $start = hrtime(\true) / 1000000.0;
        $uid = uniqid('', \true);
        $this->logger->info('Request:', ['url' => $request->getUri()->__toString(), 'uid' => $uid]);
        return $next($request)->then(function (ResponseInterface $response) use ($start, $uid, $request) {
            $content = $response->getBody()->getContents();
            if (json_decode($content)) {
                $content = json_encode(json_decode($content), \JSON_PRETTY_PRINT);
            }
            $this->logger->info(sprintf('Received response:'), ['content' => $content, 'uid' => $uid]);
            return $response;
        }, function (Exception $exception) use ($request, $start, $uid) {
            $milliseconds = (int) round(hrtime(\true) / 1000000.0 - $start);
            if ($exception instanceof Exception\HttpException) {
                $formattedResponse = method_exists($this->formatter, 'formatResponseForRequest') ? $this->formatter->formatResponseForRequest($exception->getResponse(), $exception->getRequest()) : $this->formatter->formatResponse($exception->getResponse());
                $this->logger->error(sprintf("Error:\n%s\nwith response:\n%s", $exception->getMessage(), $formattedResponse), ['exception' => $exception, 'milliseconds' => $milliseconds, 'uid' => $uid]);
            } else {
                $this->logger->error(sprintf("Error:\n%s\nwhen sending request:\n%s", $exception->getMessage(), $this->formatter->formatRequest($request)), ['exception' => $exception, 'milliseconds' => $milliseconds, 'uid' => $uid]);
            }
            throw $exception;
        });
    }
}
