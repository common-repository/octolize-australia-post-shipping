<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace OctolizeShippingAustraliaPostVendor\Symfony\Component\Translation;

use OctolizeShippingAustraliaPostVendor\Symfony\Contracts\Translation\LocaleAwareInterface;
use OctolizeShippingAustraliaPostVendor\Symfony\Contracts\Translation\TranslatorInterface;
use OctolizeShippingAustraliaPostVendor\Symfony\Contracts\Translation\TranslatorTrait;
/**
 * IdentityTranslator does not translate anything.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class IdentityTranslator implements TranslatorInterface, LocaleAwareInterface
{
    use TranslatorTrait;
}
