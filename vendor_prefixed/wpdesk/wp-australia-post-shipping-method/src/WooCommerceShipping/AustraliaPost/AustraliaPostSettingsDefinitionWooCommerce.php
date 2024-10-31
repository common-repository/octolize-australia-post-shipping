<?php

namespace OctolizeShippingAustraliaPostVendor\WPDesk\WooCommerceShipping\AustraliaPost;

use OctolizeShippingAustraliaPostVendor\WPDesk\AustraliaPostShippingService\AustraliaPostSettingsDefinition;
/**
 * Can handle global and instance settings for WooCommerce shipping method.
 */
class AustraliaPostSettingsDefinitionWooCommerce
{
    private $global_method_fields = [AustraliaPostSettingsDefinition::SHIPPING_METHOD_TITLE, AustraliaPostSettingsDefinition::API_SETTINGS_TITLE, AustraliaPostSettingsDefinition::API_KEY, AustraliaPostSettingsDefinition::ORIGIN_SETTINGS_TITLE, AustraliaPostSettingsDefinition::CUSTOM_ORIGIN, AustraliaPostSettingsDefinition::ORIGIN_ADDRESS, AustraliaPostSettingsDefinition::ORIGIN_CITY, AustraliaPostSettingsDefinition::ORIGIN_POSTCODE, AustraliaPostSettingsDefinition::ORIGIN_COUNTRY, AustraliaPostSettingsDefinition::ADVANCED_OPTIONS_TITLE, AustraliaPostSettingsDefinition::DEBUG_MODE, AustraliaPostSettingsDefinition::API_STATUS];
    /**
     * Form fields.
     *
     * @var array
     */
    private $form_fields;
    /**
     * AustraliaPostSettingsDefinitionWooCommerce constructor.
     *
     * @param array $form_fields Form fields.
     */
    public function __construct(array $form_fields)
    {
        $this->form_fields = $form_fields;
    }
    /**
     * Get form fields.
     *
     * @return array
     */
    public function get_form_fields()
    {
        return $this->filter_instance_fields($this->form_fields, \false);
    }
    /**
     * Get instance form fields.
     *
     * @return array
     */
    public function get_instance_form_fields()
    {
        return $this->filter_instance_fields($this->form_fields, \true);
    }
    /**
     * Get global method fields.
     *
     * @return array
     */
    protected function get_global_method_fields()
    {
        return $this->global_method_fields;
    }
    /**
     * Filter instance form fields.
     *
     * @param array $all_fields .
     * @param bool  $instance_fields .
     *
     * @return array
     */
    private function filter_instance_fields(array $all_fields, $instance_fields)
    {
        $fields = array();
        foreach ($all_fields as $key => $field) {
            $is_instance_field = !in_array($key, $this->get_global_method_fields(), \true);
            if ($instance_fields && $is_instance_field || !$instance_fields && !$is_instance_field) {
                $fields[$key] = $field;
            }
        }
        return $fields;
    }
}
