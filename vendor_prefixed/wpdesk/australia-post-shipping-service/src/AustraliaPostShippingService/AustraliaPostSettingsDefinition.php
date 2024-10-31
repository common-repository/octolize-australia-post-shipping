<?php

namespace OctolizeShippingAustraliaPostVendor\WPDesk\AustraliaPostShippingService;

use OctolizeShippingAustraliaPostVendor\WPDesk\AbstractShipping\Settings\SettingsDefinition;
use OctolizeShippingAustraliaPostVendor\WPDesk\AbstractShipping\Settings\SettingsValues;
use OctolizeShippingAustraliaPostVendor\WPDesk\WooCommerceShipping\FreeShipping\FreeShippingFields;
use OctolizeShippingAustraliaPostVendor\WPDesk\WooCommerceShipping\ShippingMethod\RateMethod\Fallback\FallbackRateMethod;
use OctolizeShippingAustraliaPostVendor\WPDesk\WooCommerceShipping\ShopSettings;
use OctolizeShippingAustraliaPostVendor\WPDesk\WooCommerceShipping\WooCommerceNotInitializedException;
/**
 * A class that defines the basic settings for the shipping method.
 */
class AustraliaPostSettingsDefinition extends SettingsDefinition
{
    const CUSTOM_SERVICES_CHECKBOX_CLASS = 'wpdesk_wc_shipping_custom_service_checkbox';
    const SHIPPING_METHOD_TITLE = 'shipping_method_title';
    const API_SETTINGS_TITLE = 'api_settings_title';
    const API_KEY = 'api_key';
    const TESTING = 'testing';
    const ORIGIN_SETTINGS_TITLE = 'origin_settings_title';
    const CUSTOM_ORIGIN = 'custom_origin';
    const ORIGIN_ADDRESS = 'origin_address';
    const ORIGIN_CITY = 'origin_city';
    const ORIGIN_POSTCODE = 'origin_postcode';
    const ORIGIN_COUNTRY = 'origin_country';
    const ADVANCED_OPTIONS_TITLE = 'advanced_options_title';
    const DEBUG_MODE = 'debug_mode';
    const API_STATUS = 'api_status';
    const METHOD_SETTINGS_TITLE = 'method_settings_title';
    const TITLE = 'title';
    const FALLBACK = 'fallback';
    const CUSTOM_SERVICES = 'custom_services';
    const SERVICES = 'services';
    const RATE_ADJUSTMENTS_TITLE = 'rate_adjustments_title';
    const REMOVE_GST = 'remove_gst';
    const FREE_SHIPPING = 'free_shipping';
    const PACKAGE_SETTINGS_TITLE = 'package_settings_title';
    const PACKAGE_LENGTH = 'package_length';
    const PACKAGE_WIDTH = 'package_width';
    const PACKAGE_HEIGHT = 'package_height';
    const PACKAGE_WEIGHT = 'package_weight';
    const DEFAULT_API_KEY = 'db49d010-7c85-4f56-875c-39697839cfe7';
    /**
     * Shop settings.
     *
     * @var ShopSettings
     */
    private $shop_settings;
    /**
     * AustraliaPostSettingsDefinition constructor.
     *
     * @param ShopSettings $shop_settings Shop settings.
     */
    public function __construct(ShopSettings $shop_settings)
    {
        $this->shop_settings = $shop_settings;
    }
    /**
     * Validate settings.
     *
     * @param SettingsValues $settings Settings.
     *
     * @return bool
     */
    public function validate_settings(SettingsValues $settings): bool
    {
        return \true;
    }
    /**
     * Prepare country state options.
     *
     * @return array
     */
    private function prepare_country_state_options(): array
    {
        try {
            $countries = $this->shop_settings->get_countries();
        } catch (WooCommerceNotInitializedException $e) {
            $countries = array();
        }
        $country_state_options = $countries;
        foreach ($country_state_options as $country_code => $country) {
            $states = $this->shop_settings->get_states($country_code);
            if ($states) {
                unset($country_state_options[$country_code]);
                foreach ($states as $state_code => $state_name) {
                    $country_state_options[$country_code . ':' . $state_code] = $country . ' &mdash; ' . $state_name;
                }
            }
        }
        return $country_state_options;
    }
    /**
     * Initialise Settings Form Fields.
     *
     * @return array
     */
    public function get_form_fields()
    {
        $services = new AustraliaPostServices();
        $docs_link = 'https://octol.io/ap-method-docs';
        $connection_fields = array(self::SHIPPING_METHOD_TITLE => array('title' => __('Australia Post', 'octolize-australia-post-shipping'), 'type' => 'title', 'description' => sprintf(
            // Translators: docs link.
            __('These are the Australia Post Live Rates plugin general settings. In order to learn more about its configuration please refer to its %1$sdedicated documentation →%2$s', 'octolize-australia-post-shipping'),
            '<a href="' . $docs_link . '" target="_blank">',
            '</a>'
        ), 'default' => ''), self::API_SETTINGS_TITLE => array(
            'title' => __('API Settings', 'octolize-australia-post-shipping'),
            'type' => 'title',
            // Translators: link.
            'description' => sprintf(__('Enter your Australia Post account credentials in order to establish the API connection required to obtain the live rates. If you do not have the Australia Post account yet, you can register a new account directly at %1$shttps://developers.auspost.com.au/apis/pacpcs-registration →%2$s', 'octolize-australia-post-shipping'), '<a href="https://developers.auspost.com.au/apis/pacpcs-registration" target="_blank">', '</a>'),
        ), self::API_KEY => array('title' => __('API Key *', 'octolize-australia-post-shipping'), 'type' => 'text', 'custom_attributes' => array('required' => 'required'), 'description' => __('Please replace the default Australia Post API Key with your own one you have obtained in the account registration process.', 'octolize-australia-post-shipping'), 'desc_tip' => \false, 'default' => self::DEFAULT_API_KEY));
        if ($this->shop_settings->is_testing()) {
            $connection_fields[self::TESTING] = ['title' => __('Test Credentials', 'fedex-shipping-service'), 'type' => 'checkbox', 'label' => __('Enable to use test credentials', 'fedex-shipping-service'), 'desc_tip' => \true, 'default' => 'no'];
        }
        $fields = array(self::ADVANCED_OPTIONS_TITLE => array('title' => __('Advanced Options', 'octolize-australia-post-shipping'), 'type' => 'title'), self::DEBUG_MODE => array('title' => __('Debug Mode', 'octolize-australia-post-shipping'), 'label' => __('Enable debug mode', 'octolize-australia-post-shipping'), 'type' => 'checkbox', 'description' => __('Enable debug mode to display additional tech information, incl. the data sent to Australia Post API, visible only for Admins and Shop Managers in the cart and checkout.', 'octolize-australia-post-shipping'), 'desc_tip' => \true, 'default' => 'no'));
        $instance_fields = array(self::METHOD_SETTINGS_TITLE => array('title' => __('Method Settings', 'octolize-australia-post-shipping'), 'description' => __('Manage the way how the Australia Post services are displayed in the cart and checkout.', 'octolize-australia-post-shipping'), 'type' => 'title'), self::TITLE => array('title' => __('Method Title', 'octolize-australia-post-shipping'), 'type' => 'text', 'description' => __('Define the Australia Post shipping method title which should be used in the cart/checkout when the Fallback option was triggered.', 'octolize-australia-post-shipping'), 'default' => __('Australia Post Live Rates', 'octolize-australia-post-shipping'), 'desc_tip' => \true), self::FALLBACK => array('type' => FallbackRateMethod::FIELD_TYPE_FALLBACK, 'description' => __('Enable to offer flat rate cost for shipping so that the user can still checkout, if API for some reason returns no matching rates.', 'octolize-australia-post-shipping'), 'default' => ''), self::FREE_SHIPPING => array('title' => __('Free Shipping', 'octolize-australia-post-shipping'), 'type' => FreeShippingFields::FIELD_TYPE_FREE_SHIPPING, 'default' => ''), self::CUSTOM_SERVICES => array('title' => __('Services', 'octolize-australia-post-shipping'), 'label' => __('Enable the services\' custom settings', 'octolize-australia-post-shipping'), 'type' => 'checkbox', 'description' => __('Decide which services should be displayed and which not, change their names and order. Please mind that enabling a service does not guarantee it will be visible in the cart/checkout. It has to be available for the provided package weight, origin and destination in order to be displayed.', 'octolize-australia-post-shipping'), 'desc_tip' => \true, 'class' => self::CUSTOM_SERVICES_CHECKBOX_CLASS, 'default' => 'no'), self::SERVICES => array('title' => __('Services Table', 'octolize-australia-post-shipping'), 'type' => 'services', 'default' => '', 'options' => $services->get_all_services()), self::PACKAGE_SETTINGS_TITLE => array('title' => __('Package Settings', 'octolize-australia-post-shipping'), 'description' => sprintf(__('Define the package details including its dimensions and weight which will be used as default for this shipping method.', 'octolize-australia-post-shipping')), 'type' => 'title'), self::PACKAGE_LENGTH => array('title' => __('Length [cm] *', 'octolize-australia-post-shipping'), 'type' => 'number', 'description' => __('Enter only a numeric value without the metric symbol.', 'octolize-australia-post-shipping'), 'desc_tip' => \true, 'custom_attributes' => array('required' => 'required')), self::PACKAGE_WIDTH => array('title' => __('Width [cm] *', 'octolize-australia-post-shipping'), 'type' => 'number', 'description' => __('Enter only a numeric value without the metric symbol.', 'octolize-australia-post-shipping'), 'desc_tip' => \true, 'custom_attributes' => array('required' => 'required')), self::PACKAGE_HEIGHT => array('title' => __('Height [cm] *', 'octolize-australia-post-shipping'), 'type' => 'number', 'description' => __('Enter only a numeric value without the metric symbol.', 'octolize-australia-post-shipping'), 'desc_tip' => \true, 'custom_attributes' => array('required' => 'required')), self::PACKAGE_WEIGHT => array('title' => __('Default weight [kg] *', 'octolize-australia-post-shipping'), 'type' => 'number', 'description' => __('Enter the package weight value which will be used as default if none of the products\' in the cart individual weight has been filled in or if the cart total weight equals 0 kg.', 'octolize-australia-post-shipping'), 'desc_tip' => \true, 'custom_attributes' => array('required' => 'required')), self::RATE_ADJUSTMENTS_TITLE => array('title' => __('Rates Adjustments', 'octolize-australia-post-shipping'), 'description' => sprintf(__('Use these settings and adjust them to your needs to get more accurate rates. Read %1$swhat affects the Australia Post rates in Australia Post WooCommerce plugin →%2$s', 'octolize-australia-post-shipping'), sprintf('<a href="%s" target="_blank">', __('https://octol.io/ap-free-rates', 'octolize-australia-post-shipping')), '</a>'), 'type' => 'title'), self::REMOVE_GST => array('title' => __('Goods and Services Tax (GST)', 'octolize-australia-post-shipping'), 'label' => __('Remove the GST', 'octolize-australia-post-shipping'), 'type' => 'checkbox', 'description' => __('Tick this checkbox in order to strip the 10% GST tax value from the shipping rates coming from Australia Post.', 'octolize-australia-post-shipping'), 'desc_tip' => \false, 'default' => 'no'));
        return $connection_fields + $fields + $instance_fields;
    }
}
