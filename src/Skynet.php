<?php


namespace Claassenmarius\PhpSkynet;

use GuzzleHttp\Client;

class Skynet
{
    /**
     * Your Skynet account Username
     *
     * @var string
     */
    private string $username;

    /**
     * Your Skynet account password
     *
     * @var string
     */
    private string $password;

    /**
     * Your Skynet account System Id
     *
     * @var string
     */
    private string $systemId;

    /**
     * Your Skynet account number
     *
     * @var string
     */
    private string $accountNumber;

    /**
     * The Guzzle HTTP client
     *
     * @var Client
     */
    protected Client $client;

    /**
     * Create a new Skynet instance
     *
     * @param string $username
     * @param string $password
     * @param string $systemId
     * @param string $accountNumber
     * @param Client|null $client
     */
    public function __construct(
        string $username,
        string $password,
        string $systemId,
        string $accountNumber,
        Client $client = null,
    ) {
        $this->username = $username;
        $this->password = $password;
        $this->systemId = $systemId;
        $this->accountNumber = $accountNumber;
        $this->client = $client ?: new Client([
            'base_uri' => 'https://api.skynet.co.za:3227/api/',
        ]);
    }

    /**
     * Get a Skynet generated security token
     *
     * @return Response
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function securityToken(): Response
    {
        $response = $this->client->post('Security/GetSecurityToken', [
            'json' => [
                "Username" => $this->username,
                "Password" => $this->password,
                "SystemId" => $this->systemId,
                "AccountNumber" => $this->accountNumber,
            ],
        ]);

        return new Response($response);
    }

    /**
     * Validate a suburb and postal code combination
     *
     * @param array $location
     * @return Response
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function validateSuburbAndPostalCode(array $location): Response
    {
        $response = $this->client->post('Validation/ValidateSuburbPostalCode', [
            'json' => [
                'suburb' => $location['suburb'],
                'postalCode' => $location['postal-code'],
            ],
        ]);

        return new Response($response);
    }

    /**
     * Get a list of postal codes for a suburb
     *
     * @param string $suburb
     * @return Response
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function postalCodesFromSuburb(string $suburb)
    {
        $response = $this->client->post('Validation/GetPostalCode', [
            'json' => [
                'SecurityToken' => $this->securityToken()->json()['SecurityToken'],
                'suburbName' => $suburb,
            ],
        ]);

        return new Response($response);
    }

    /**
     * Get a quote for a parcel
     *
     * @param array $parcelData
     * @return Response
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function quote(array $parcelData)
    {
        $response = $this->client->post('Financial/GetQuote', [
           'json' => [
               'SecurityToken' => $this->securityToken(),
               'AccountNumber' => $this->accountNumber,
               'FromCity' => $parcelData['collect-city'],
               'ToCity' => $parcelData['deliver-city'],
               'ServiceType' => $parcelData['service-type'],
               'InsuranceType' => $parcelData['insurance-type'],
               'InsuranceAmount' => $parcelData['parcel-insurance'],
               'DestinationPCode' => $parcelData['deliver-postcode'],
               'ParcelList' => [
                   [
                       'parcel_number' => "1",
                       'parcel_length' => $parcelData['parcel-length'],
                       'parcel_breadth' => $parcelData['parcel-width'],
                       'parcel_height' => $parcelData['parcel-height'],
                       'parcel_mass' => $parcelData['parcel-weight'],
                   ],
               ],
           ],
        ]);

        return new Response($response);
    }

    /**
     * Get ETA between two locations
     *
     * @param array $locations
     * @return Response
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function deliveryETA(array $locations)
    {
        $response = $this->client->post('Waybill/GetWaybillETA', [
            'json' => [
                'SecurityToken' => $this->securityToken(),
                'AccountNumber' => $this->accountNumber,
                'FromSuburb' => $locations['from-suburb'],
                'FromPostCode' => $locations['from-postcode'],
                'ToSuburb' => $locations['to-suburb'],
                'ToPostCode' => $locations['to-postcode'],
                'ServiceType' => $locations['service-type'],
            ],
        ]);

        return new Response($response);
    }

    /**
     * Generate a waybill
     *
     * @param array $waybillData
     * @return Response
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function createWaybill(array $waybillData)
    {
        $response = $this->client->post('waybill/CreateWaybill', [
            'json' => [
                "SecurityToken" => $this->securityToken()->json()['SecurityToken'],
                "AccountNumber" => $this->accountNumber,
                "CompanyName" => $waybillData['company-name'] ?? null,
                "CustomerReference" => $waybillData['customer-reference'],
                "WaybillNumber" => $waybillData['waybill-number'] ?? null,
                "GenerateWaybillNumber" => $waybillData['generate-waybill-number'] ?? false,
                "ServiceType" => $waybillData['service-type'],
                "CollectionDate" => $waybillData['collection-date'],
                "DeliveryDate" => $waybillData['delivery-date'] ?? null,
                "Instructions" => $waybillData['instructions'] ?? null,
                "FromAddressName" => $waybillData['from-address-name'] ?? null,
                "FromAddress1" => $waybillData['from-address-1'],
                "FromAddress2" => $waybillData['from-address-2'] ?? null,
                "FromAddress3" => $waybillData['from-address-3'] ?? null,
                "FromAddress4" => $waybillData['from-address-4'] ?? null,
                "FromSuburb" => $waybillData['from-suburb'],
                "FromCity" => $waybillData['from-city'] ?? null,
                "FromPostCode" => $waybillData['from-postcode'],
                "FromAddressLatitude" => $waybillData['from-address-latitude'] ?? null,
                "FromAddressLongitude" => $waybillData['from-address-longitude'] ?? null,
                "FromTelephone" => $waybillData['from-telephone'] ?? null,
                "FromFax" => $waybillData['from-fax'] ?? null,
                "FromOfficeTelephonenumber" => $waybillData['from-office-telephone-number'] ?? null,
                "FromAlternativeContactName" => $waybillData['from-alternative-contact-name'] ?? null,
                "FromAlternativeContactNumber" => $waybillData['from-alternative-contact-number'] ?? null,
                "FromBuildingComplex" => $waybillData['from-building-complex'] ?? null,
                "ToAddressName" => $waybillData['to-address-name'] ?? null,
                "ToAddress1" => $waybillData['to-address-1'],
                "ToAddress2" => $waybillData['to-address-2'] ?? null,
                "ToAddress3" => $waybillData['to-address-3'] ?? null,
                "ToAddress4" => $waybillData['to-address-4'] ?? null,
                "ToSuburb" => $waybillData['to-suburb'],
                "ToCity" => $waybillData['to-city'] ?? null,
                "ToPostCode" => $waybillData['to-postcode'],
                "ToAddressLatitude" => $waybillData['to-address-latitude'] ?? null,
                "ToAddressLongitude" => $waybillData['to-address-longitude'] ?? null,
                "ToTelephone" => $waybillData['to-telephone'] ?? null,
                "ToFax" => $waybillData['to-fax'] ?? null,
                "ToOfficeTelephonenumber" => $waybillData['to-office-telephone-number'] ?? null,
                "ToAlternativeContactName" => $waybillData['to-alternative-contact-name'] ?? null,
                "ToAlternativeContactNumber" => $waybillData['to-alternative-contact-number'] ?? null,
                "ToBuildingComplex" => $waybillData['to-building-complex'] ?? null,
                "ReadyTime" => $waybillData['ready-time'] ?? null,
                "OpenTill" => $waybillData['open-till'] ?? null,
                "InsuranceType" => $waybillData['insurance-type'] ?? '1',
                "InsuranceAmount" => $waybillData['insurance-amount'] ?? '0',
                "Security" => $waybillData['security'] ?? 'N',
                "ParcelList" => [[
                    "parcel_number" => "1",
                    "parcel_length" => $waybillData['parcel-length'],
                    "parcel_breadth" => $waybillData['parcel-width'],
                    "parcel_height" => $waybillData['parcel-height'] ,
                    "parcel_mass" => $waybillData['parcel-weight'],
                    "parcel_description" => $waybillData['parcel-description'] ?? null,
                    "parcel_reference" => $waybillData['parcel-reference'],
                ]],
                "OffSiteCollection" => $waybillData['offsite-collection'] ?? false,
            ],
        ]);

        return new Response($response);
    }

    /**
     * Get a waybill POD Image
     *
     * @param string $waybillNumber
     * @return Response
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function waybillPOD(string $waybillNumber)
    {
        $response = $this->client->post('Waybill/GetWaybillPOD', [
            'json' => [
                'SecurityToken' => $this->securityToken(),
                'WaybillNumber' => $waybillNumber,
            ],
        ]);

        return new Response($response);
    }

    /**
     * Track a waybill
     *
     * @param string $waybillNumber
     * @return Response
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function trackWaybill(string $waybillNumber)
    {
        $response = $this->client->get('waybill/GetWaybillTracking', [
            'query' => [
                'WaybillReference' => $waybillNumber,
            ],
        ]);

        return new Response($response);
    }
}
