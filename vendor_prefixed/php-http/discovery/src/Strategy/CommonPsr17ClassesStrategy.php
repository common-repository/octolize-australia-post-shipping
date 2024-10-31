<?php

namespace OctolizeShippingAustraliaPostVendor\Http\Discovery\Strategy;

use OctolizeShippingAustraliaPostVendor\Psr\Http\Message\RequestFactoryInterface;
use OctolizeShippingAustraliaPostVendor\Psr\Http\Message\ResponseFactoryInterface;
use OctolizeShippingAustraliaPostVendor\Psr\Http\Message\ServerRequestFactoryInterface;
use OctolizeShippingAustraliaPostVendor\Psr\Http\Message\StreamFactoryInterface;
use OctolizeShippingAustraliaPostVendor\Psr\Http\Message\UploadedFileFactoryInterface;
use OctolizeShippingAustraliaPostVendor\Psr\Http\Message\UriFactoryInterface;
/**
 * @internal
 *
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 *
 * Don't miss updating src/Composer/Plugin.php when adding a new supported class.
 */
final class CommonPsr17ClassesStrategy implements DiscoveryStrategy
{
    /**
     * @var array
     */
    private static $classes = [RequestFactoryInterface::class => ['OctolizeShippingAustraliaPostVendor\Phalcon\Http\Message\RequestFactory', 'OctolizeShippingAustraliaPostVendor\Nyholm\Psr7\Factory\Psr17Factory', 'OctolizeShippingAustraliaPostVendor\GuzzleHttp\Psr7\HttpFactory', 'OctolizeShippingAustraliaPostVendor\Http\Factory\Diactoros\RequestFactory', 'OctolizeShippingAustraliaPostVendor\Http\Factory\Guzzle\RequestFactory', 'OctolizeShippingAustraliaPostVendor\Http\Factory\Slim\RequestFactory', 'OctolizeShippingAustraliaPostVendor\Laminas\Diactoros\RequestFactory', 'OctolizeShippingAustraliaPostVendor\Slim\Psr7\Factory\RequestFactory', 'OctolizeShippingAustraliaPostVendor\HttpSoft\Message\RequestFactory'], ResponseFactoryInterface::class => ['OctolizeShippingAustraliaPostVendor\Phalcon\Http\Message\ResponseFactory', 'OctolizeShippingAustraliaPostVendor\Nyholm\Psr7\Factory\Psr17Factory', 'OctolizeShippingAustraliaPostVendor\GuzzleHttp\Psr7\HttpFactory', 'OctolizeShippingAustraliaPostVendor\Http\Factory\Diactoros\ResponseFactory', 'OctolizeShippingAustraliaPostVendor\Http\Factory\Guzzle\ResponseFactory', 'OctolizeShippingAustraliaPostVendor\Http\Factory\Slim\ResponseFactory', 'OctolizeShippingAustraliaPostVendor\Laminas\Diactoros\ResponseFactory', 'OctolizeShippingAustraliaPostVendor\Slim\Psr7\Factory\ResponseFactory', 'OctolizeShippingAustraliaPostVendor\HttpSoft\Message\ResponseFactory'], ServerRequestFactoryInterface::class => ['OctolizeShippingAustraliaPostVendor\Phalcon\Http\Message\ServerRequestFactory', 'OctolizeShippingAustraliaPostVendor\Nyholm\Psr7\Factory\Psr17Factory', 'OctolizeShippingAustraliaPostVendor\GuzzleHttp\Psr7\HttpFactory', 'OctolizeShippingAustraliaPostVendor\Http\Factory\Diactoros\ServerRequestFactory', 'OctolizeShippingAustraliaPostVendor\Http\Factory\Guzzle\ServerRequestFactory', 'OctolizeShippingAustraliaPostVendor\Http\Factory\Slim\ServerRequestFactory', 'OctolizeShippingAustraliaPostVendor\Laminas\Diactoros\ServerRequestFactory', 'OctolizeShippingAustraliaPostVendor\Slim\Psr7\Factory\ServerRequestFactory', 'OctolizeShippingAustraliaPostVendor\HttpSoft\Message\ServerRequestFactory'], StreamFactoryInterface::class => ['OctolizeShippingAustraliaPostVendor\Phalcon\Http\Message\StreamFactory', 'OctolizeShippingAustraliaPostVendor\Nyholm\Psr7\Factory\Psr17Factory', 'OctolizeShippingAustraliaPostVendor\GuzzleHttp\Psr7\HttpFactory', 'OctolizeShippingAustraliaPostVendor\Http\Factory\Diactoros\StreamFactory', 'OctolizeShippingAustraliaPostVendor\Http\Factory\Guzzle\StreamFactory', 'OctolizeShippingAustraliaPostVendor\Http\Factory\Slim\StreamFactory', 'OctolizeShippingAustraliaPostVendor\Laminas\Diactoros\StreamFactory', 'OctolizeShippingAustraliaPostVendor\Slim\Psr7\Factory\StreamFactory', 'OctolizeShippingAustraliaPostVendor\HttpSoft\Message\StreamFactory'], UploadedFileFactoryInterface::class => ['OctolizeShippingAustraliaPostVendor\Phalcon\Http\Message\UploadedFileFactory', 'OctolizeShippingAustraliaPostVendor\Nyholm\Psr7\Factory\Psr17Factory', 'OctolizeShippingAustraliaPostVendor\GuzzleHttp\Psr7\HttpFactory', 'OctolizeShippingAustraliaPostVendor\Http\Factory\Diactoros\UploadedFileFactory', 'OctolizeShippingAustraliaPostVendor\Http\Factory\Guzzle\UploadedFileFactory', 'OctolizeShippingAustraliaPostVendor\Http\Factory\Slim\UploadedFileFactory', 'OctolizeShippingAustraliaPostVendor\Laminas\Diactoros\UploadedFileFactory', 'OctolizeShippingAustraliaPostVendor\Slim\Psr7\Factory\UploadedFileFactory', 'OctolizeShippingAustraliaPostVendor\HttpSoft\Message\UploadedFileFactory'], UriFactoryInterface::class => ['OctolizeShippingAustraliaPostVendor\Phalcon\Http\Message\UriFactory', 'OctolizeShippingAustraliaPostVendor\Nyholm\Psr7\Factory\Psr17Factory', 'OctolizeShippingAustraliaPostVendor\GuzzleHttp\Psr7\HttpFactory', 'OctolizeShippingAustraliaPostVendor\Http\Factory\Diactoros\UriFactory', 'OctolizeShippingAustraliaPostVendor\Http\Factory\Guzzle\UriFactory', 'OctolizeShippingAustraliaPostVendor\Http\Factory\Slim\UriFactory', 'OctolizeShippingAustraliaPostVendor\Laminas\Diactoros\UriFactory', 'OctolizeShippingAustraliaPostVendor\Slim\Psr7\Factory\UriFactory', 'OctolizeShippingAustraliaPostVendor\HttpSoft\Message\UriFactory']];
    public static function getCandidates($type)
    {
        $candidates = [];
        if (isset(self::$classes[$type])) {
            foreach (self::$classes[$type] as $class) {
                $candidates[] = ['class' => $class, 'condition' => [$class]];
            }
        }
        return $candidates;
    }
}
