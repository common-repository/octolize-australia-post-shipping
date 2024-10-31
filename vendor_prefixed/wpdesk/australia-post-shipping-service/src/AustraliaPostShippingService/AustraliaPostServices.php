<?php

namespace OctolizeShippingAustraliaPostVendor\WPDesk\AustraliaPostShippingService;

/**
 * A class that defines Australia Post services.
 */
class AustraliaPostServices
{
    /**
     * @return array
     */
    private function get_services(): array
    {
        return ['domestic_au' => [
            // Domestic - Parcel
            'AUS_PARCEL_REGULAR' => __('Australia Post Parcel Post', 'octolize-australia-post-shipping'),
            'AUS_PARCEL_EXPRESS' => __('Australia Post Express Post', 'octolize-australia-post-shipping'),
        ], 'international' => [
            // International - Parcel
            'INT_PARCEL_STD_OWN_PACKAGING' => __('Australia Post International Standard', 'octolize-australia-post-shipping'),
            'INT_PARCEL_EXP_OWN_PACKAGING' => __('Australia Post International Express', 'octolize-australia-post-shipping'),
        ]];
    }
    public function get_all_services()
    {
        return array_merge($this->get_services_domestic_au(), $this->get_services_international());
    }
    /**
     * @return array
     */
    public function get_services_domestic_au(): array
    {
        return $this->get_services()['domestic_au'];
    }
    /**
     * @return array
     */
    public function get_services_international(): array
    {
        return $this->get_services()['international'];
    }
}
