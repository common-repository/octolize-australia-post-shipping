<?php

namespace OctolizeShippingAustraliaPostVendor\WPDesk\AustraliaPostShippingService\Api;

use OctolizeShippingAustraliaPostVendor\WPDesk\AbstractShipping\Rate\Money;
use OctolizeShippingAustraliaPostVendor\Fontis\Auspost\Api\Postage\International\Parcel\Cost\CalculationResponse;
use OctolizeShippingAustraliaPostVendor\WPDesk\AbstractShipping\Rate\SingleRate;
/**
 * Can convert API response to rates.
 */
class AustraliaPostInternationalRateReplyInterpretation extends AustraliaPostRateReplyInterpretation
{
    /**
     * @var CalculationResponse[]
     */
    private $response;
    /**
     * @param CalculationResponse[] $response
     * @param bool $is_tax_enabled
     */
    public function __construct(array $response, $is_tax_enabled)
    {
        $this->response = $response;
        $this->is_tax_enabled = $is_tax_enabled;
    }
    /**
     * Get rates from Australia Post API response.
     *
     * @return SingleRate[]
     */
    public function get_rates()
    {
        $rates = [];
        foreach ($this->response as $service => $response) {
            $rate = new SingleRate();
            $rate->service_type = $service;
            $rate->service_name = $response->getService();
            $total_charge = new Money();
            $total_charge->amount = $response->getTotalCost();
            $total_charge->currency = 'AUD';
            $rate->total_charge = $total_charge;
            $rates[] = $rate;
        }
        return $rates;
    }
}
