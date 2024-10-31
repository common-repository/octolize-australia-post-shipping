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
namespace OctolizeShippingAustraliaPostVendor\Fontis\Auspost\Model\Postage;

final class Weight
{
    /**
     * @var string
     */
    private $name;
    /**
     * @var float|null
     */
    private $value;
    /**
     * @var string|null
     */
    private $code;
    /**
     * @param string $name
     * @param float|null $value
     * @param string|null $code
     */
    public function __construct(string $name, ?float $value = null, ?string $code = null)
    {
        $this->code = $code;
        $this->name = $name;
        $this->value = $value;
    }
    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
    /**
     * @return string|null
     */
    public function getCode(): ?string
    {
        return $this->code;
    }
    /**
     * @return float|null
     */
    public function getValue(): ?float
    {
        return $this->value;
    }
}
