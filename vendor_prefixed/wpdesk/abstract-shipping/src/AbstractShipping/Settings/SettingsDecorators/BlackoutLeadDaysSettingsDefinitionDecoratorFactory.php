<?php

/**
 * Class BlackoutLeadDaysSettingsDefinitionDecoratorFactory
 *
 * @package WPDesk\AbstractShipping\Settings\SettingsDecorators
 */
namespace OctolizeShippingAustraliaPostVendor\WPDesk\AbstractShipping\Settings\SettingsDecorators;

/**
 * Can create Blackout Lead Days settings decorator.
 */
class BlackoutLeadDaysSettingsDefinitionDecoratorFactory extends AbstractDecoratorFactory
{
    const OPTION_ID = 'blackout_lead_days';
    /**
     * @return string
     */
    public function get_field_id()
    {
        return self::OPTION_ID;
    }
    /**
     * @return array
     */
    protected function get_field_settings()
    {
        return array('title' => __('Blackout Lead Days', 'octolize-australia-post-shipping'), 'type' => 'multiselect', 'description' => __('Blackout Lead Days are used to define days of the week when shop is not processing orders.', 'octolize-australia-post-shipping'), 'options' => array('1' => __('Monday', 'octolize-australia-post-shipping'), '2' => __('Tuesday', 'octolize-australia-post-shipping'), '3' => __('Wednesday', 'octolize-australia-post-shipping'), '4' => __('Thursday', 'octolize-australia-post-shipping'), '5' => __('Friday', 'octolize-australia-post-shipping'), '6' => __('Saturday', 'octolize-australia-post-shipping'), '7' => __('Sunday', 'octolize-australia-post-shipping')), 'custom_attributes' => array('size' => 7), 'class' => 'wc-enhanced-select', 'desc_tip' => \true, 'default' => '');
    }
}
