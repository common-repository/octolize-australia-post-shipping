<?php

declare (strict_types=1);
namespace OctolizeShippingAustraliaPostVendor\Http\Client\Common\Exception;

use OctolizeShippingAustraliaPostVendor\Http\Client\Exception\HttpException;
/**
 * Redirect location cannot be chosen.
 *
 * @author Joel Wurtz <joel.wurtz@gmail.com>
 */
final class MultipleRedirectionException extends HttpException
{
}
