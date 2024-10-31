<?php

namespace OctolizeShippingAustraliaPostVendor\WPDesk\AustraliaPostShippingService\Api;

use OctolizeShippingAustraliaPostVendor\Fontis\Auspost\Api\Postage\International\Parcel\Cost\CalculationParams;
use OctolizeShippingAustraliaPostVendor\Fontis\Auspost\Api\Postage\International\Parcel\Cost\CalculationResponse;
use OctolizeShippingAustraliaPostVendor\WPDesk\AbstractShipping\Shipment\Weight;
/**
 * Can build request to Australia Post API.
 */
class AustraliaPostInternationalRateRequestBuilder extends AustraliaPostRateRequestBuilder
{
    /**
     * @return CalculationResponse[]
     */
    public function get_response()
    {
        $this->check_packages();
        $package = $this->shipment->packages[0];
        $response = [];
        foreach ($this->services as $service => $service_name) {
            $response[$service] = $this->auspost->postage()->calculateInternationalParcelPostage(new CalculationParams($this->shipment->ship_to->address->country_code, $this->calculate_package_weight($package, Weight::WEIGHT_UNIT_KG), $service));
        }
        return $response;
    }
}
