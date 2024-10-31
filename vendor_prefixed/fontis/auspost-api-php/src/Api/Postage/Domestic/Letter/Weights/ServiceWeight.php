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
namespace OctolizeShippingAustraliaPostVendor\Fontis\Auspost\Api\Postage\Domestic\Letter\Weights;

use Exception;
use OctolizeShippingAustraliaPostVendor\Fontis\Auspost\Api\Postage\Domestic\Letter\Cost\ParseJsonError;
use OctolizeShippingAustraliaPostVendor\Fontis\Auspost\Exception\EmptyServiceResponse;
use OctolizeShippingAustraliaPostVendor\Fontis\Auspost\HttpClient\HttpApi;
use OctolizeShippingAustraliaPostVendor\Fontis\Auspost\Model\Postage\Weight;
use OctolizeShippingAustraliaPostVendor\Fontis\Auspost\Model\Postage\WeightCollection;
use OctolizeShippingAustraliaPostVendor\Psr\Http\Message\ResponseInterface;
class ServiceWeight
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
     * @return WeightCollection
     */
    public function listWeights(): WeightCollection
    {
        $response = $this->httpClient->get('postage/letter/domestic/weight.json');
        $responseBody = $this->parseResponse($response);
        if (!isset($responseBody["weights"]) || !isset($responseBody["weights"]["weight"]) || !count($responseBody["weights"]["weight"])) {
            return new WeightCollection([]);
        }
        $result = [];
        foreach ($responseBody["weights"]["weight"] as $weight) {
            $name = $weight["name"] ?? "";
            $value = isset($weight["value"]) ? (float) $weight["value"] : null;
            $code = $weight["code"] ?? null;
            array_push($result, new Weight($name, $value, $code));
        }
        $weightCollection = new WeightCollection($result);
        return $weightCollection;
    }
    /**
     * @param ResponseInterface $response
     * @return array
     * @throws Exception If invalid response.
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
