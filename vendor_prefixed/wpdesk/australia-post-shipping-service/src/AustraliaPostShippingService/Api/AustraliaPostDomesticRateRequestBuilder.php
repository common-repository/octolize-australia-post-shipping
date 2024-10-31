<?php

namespace OctolizeShippingAustraliaPostVendor\WPDesk\AustraliaPostShippingService\Api;

use OctolizeShippingAustraliaPostVendor\Fontis\Auspost\Api\Postage\Domestic\Parcel\Cost\CalculationParams;
use OctolizeShippingAustraliaPostVendor\Fontis\Auspost\Api\Postage\Domestic\Parcel\Cost\CalculationResponse;
use OctolizeShippingAustraliaPostVendor\WPDesk\AbstractShipping\Shipment\Weight;
/**
 * Can build request to Australia Post API.
 */
class AustraliaPostDomesticRateRequestBuilder extends AustraliaPostRateRequestBuilder
{
    /**
     * @return CalculationResponse[]
     */
    public function get_response()
    {
        $this->check_packages();
        $package = $this->shipment->packages[0];
        $dimensions = $this->prepare_dimensions($package);
        $response = [];
        foreach ($this->services as $service => $service_name) {
            $response[$service] = $this->auspost->postage()->calculateDomesticParcelPostage(new CalculationParams(str_replace(' ', '', $this->shipment->ship_from->address->postal_code), str_replace(' ', '', $this->shipment->ship_to->address->postal_code), $dimensions->length, $dimensions->width, $dimensions->height, $this->calculate_package_weight($package, Weight::WEIGHT_UNIT_KG), $service));
        }
        return $response;
    }
}
