<?php

namespace OctolizeShippingAustraliaPostVendor\WPDesk\AustraliaPostShippingService\Api;

use OctolizeShippingAustraliaPostVendor\WPDesk\AbstractShipping\Rate\SingleRate;
/**
 * Can convert API response to rates.
 */
abstract class AustraliaPostRateReplyInterpretation
{
    /**
     * @var bool
     */
    protected $is_tax_enabled;
    /**
     * Has reply error.
     *
     * @return bool
     */
    public function has_reply_error()
    {
        return \false;
    }
    /**
     * Has reply warning.
     *
     * @return bool
     */
    public function has_reply_warning()
    {
        return \false;
    }
    /**
     * Get reply error message.
     *
     * @return string
     */
    public function get_reply_message()
    {
        return '';
    }
    /**
     * Get reates from Canada Post.
     *
     * @return SingleRate[]
     */
    abstract public function get_rates();
}
