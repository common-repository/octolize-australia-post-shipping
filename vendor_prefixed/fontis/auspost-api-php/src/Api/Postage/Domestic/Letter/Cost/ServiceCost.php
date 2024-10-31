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
namespace OctolizeShippingAustraliaPostVendor\Fontis\Auspost\Api\Postage\Domestic\Letter\Cost;

use Exception;
use OctolizeShippingAustraliaPostVendor\Fontis\Auspost\Exception\EmptyServiceResponse;
use OctolizeShippingAustraliaPostVendor\Fontis\Auspost\HttpClient\HttpApi;
use OctolizeShippingAustraliaPostVendor\Fontis\Auspost\Model\Postage\Cost;
use OctolizeShippingAustraliaPostVendor\Fontis\Auspost\Model\Postage\CostCollection;
use OctolizeShippingAustraliaPostVendor\Psr\Http\Message\ResponseInterface;
class ServiceCost
{
    /**
     * @var HttpApi
     */
    private $httpClient;
    /**
     * @param HttpApi $httpClient
     */
    public function __construct(HttpApi $httpClient)
    {
        $this->httpClient = $httpClient;
    }
    /**
     * @param CalculationParams $params
     * @return CalculationResponse
     */
    public function calculate(CalculationParams $params): CalculationResponse
    {
        $apiParams = $this->generateApiParam($params);
        $response = $this->httpClient->get('postage/letter/domestic/calculate', $apiParams);
        $responseBody = $this->parseResponse($response);
        $costs = [];
        if (!empty($responseBody["postage_result"])) {
            foreach ($responseBody["postage_result"]["costs"] as $cost) {
                array_push($costs, new Cost($cost["item"], (float) $cost["cost"]));
            }
        }
        $costCollection = new CostCollection($costs);
        $result = new CalculationResponse($responseBody["postage_result"]["service"], $responseBody["postage_result"]["delivery_time"], (float) $responseBody["postage_result"]["total_cost"], $costCollection);
        return $result;
    }
    /**
     * @param CalculationParams $params
     * @return array
     */
    private function generateApiParam(CalculationParams $params): array
    {
        $urlParams = ['service_code' => $params->getServiceCode(), 'weight' => $params->getWeight()];
        if (!empty($params->getOptionCode())) {
            $urlParams['option_code'] = $params->getOptionCode();
        }
        if (!empty($params->getSubOptionCode())) {
            $urlParams['suboption_code'] = $params->getSubOptionCode();
        }
        if (!empty($params->getExtraCover())) {
            $urlParams['extra_cover'] = $params->getExtraCover();
        }
        return $urlParams;
    }
    /**
     * @param ResponseInterface $response
     * @return array
     * @throws Exception If empty response.
     */
    private function parseResponse(ResponseInterface $response): array
    {
        $responseBody = (string) $response->getBody();
        if (empty($responseBody)) {
            throw new EmptyServiceResponse("The response body is empty.");
        }
        $result = json_decode($responseBody, \true);
        if (json_last_error() !== \JSON_ERROR_NONE) {
            throw new ParseJsonError('Error parsing JSON: ' . json_last_error_msg());
        }
        return $result;
    }
}
