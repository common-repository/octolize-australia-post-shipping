<?php

namespace OctolizeShippingAustraliaPostVendor\Http\Message\Authentication;

use OctolizeShippingAustraliaPostVendor\Http\Message\Authentication;
use OctolizeShippingAustraliaPostVendor\Psr\Http\Message\RequestInterface;
/**
 * Authenticate a PSR-7 Request using a token.
 *
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 */
final class Bearer implements Authentication
{
    /**
     * @var string
     */
    private $token;
    /**
     * @param string $token
     */
    public function __construct($token)
    {
        $this->token = $token;
    }
    public function authenticate(RequestInterface $request)
    {
        $header = sprintf('Bearer %s', $this->token);
        return $request->withHeader('Authorization', $header);
    }
}
