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
namespace OctolizeShippingAustraliaPostVendor\Fontis\Auspost\Helper;

use OctolizeShippingAustraliaPostVendor\Fontis\Auspost\Exception\InvalidArgument;
class Validation
{
    /**
     * @param $value
     * @throws InvalidArgument
     */
    public static function notNull($value)
    {
        if ($value === null) {
            throw new InvalidArgument("The value cannot be null.");
        }
    }
}
