<?php


namespace Claassenmarius\PhpSkynet\Tests;

use Claassenmarius\PhpSkynet\Skynet;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class SkynetTest extends TestCase
{
    public MockHandler $mock;

    public Skynet $skynet;

    protected function setUp(): void
    {
        $this->mock = new MockHandler();

        $client = new Client([
            'handler' => HandlerStack::create($this->mock),
        ]);

        $this->skynet = new Skynet(
            'iDeliverTest',
            '!D3liver1!',
            '2',
            'J99133',
            $client
        );
    }

    /** @test */
    public function it_can_get_a_security_code()
    {
        $this->mock->append(
            new Response(
                200,
                ['Content-Type' => 'application/json; charset=utf-8'],
                "{\"SecurityToken\":\"2_03ce4988-43db-45ea-9797-e69befff8d3f\"}"
            )
        );

        $response = $this->skynet->securityToken();

        $this->assertEquals(true, $response->successful());
        $this->assertEquals("{\"SecurityToken\":\"2_03ce4988-43db-45ea-9797-e69befff8d3f\"}", $response->body());
        $this->assertEquals('application/json; charset=utf-8', $response->header('Content-Type'));
    }

    /** @test */
    public function it_can_validate_suburb_and_postcode_combination()
    {
        $this->mock->append(
            new Response(
            200,
            ['Content-Type' => 'application/json; charset=utf-8'],
            true
        )
        );

        $response = $this->skynet->validateSuburbAndPostalCode([
            'suburb' => 'Brackenfell',
            'postal-code' => 7560,
        ]);

        $this->assertEquals(true, $response->successful());
        $this->assertEquals(true, $response->json());
        $this->assertEquals('application/json; charset=utf-8', $response->header('Content-Type'));
    }

    /** @test */
    public function it_can_get_postal_codes_for_a_suburb()
    {
        $expectedResponse = '[
                                {
                                    "postalCodeId": 27293,
                                    "suburb": "SWAKOPMUND",
                                    "town": "International",
                                    "province": "International",
                                    "country": "NAMIBIA (OUTLYING)",
                                    "postalCode": "NAMO",
                                    "countryId": 189,
                                    "startDate": "2020-01-01T00:00:00",
                                    "endDate": "9999-12-31T00:00:00",
                                    "isActive": true,
                                    "combined": "SWAKOPMUND, International, International, NAMO"
                                }
                            ]';

        $this->mock->append(
            new Response(
                200,
                ['Content-Type' => 'application/json; charset=utf-8'],
                "{\"SecurityToken\":\"2_03ce4988-43db-45ea-9797-e69befff8d3f\"}"
            )
        );
        $this->mock->append(
            new Response(
                200,
                ['Content-Type' => 'application/json; charset=utf-8'],
                $expectedResponse
            )
        );

        $response = $this->skynet->postalCodesFromSuburb("Swakopmund");

        $this->assertEquals(true, $response->successful());
        $this->assertEquals($expectedResponse, $response->body());
        $this->assertEquals('application/json; charset=utf-8', $response->header('Content-Type'));
    }

    /** @test */
    public function it_can_get_a_quote()
    {
        $expectedResponse = '{
                                "vat": "558.81",
                                "charges": "3725.38",
                                "errorDescription": "",
                                "errorCode": "0"
                            }';

        $this->mock->append(
            new Response(
                200,
                ['Content-Type' => 'application/json; charset=utf-8'],
                "{\"SecurityToken\":\"2_03ce4988-43db-45ea-9797-e69befff8d3f\"}"
            )
        );
        $this->mock->append(
            new Response(
                200,
                ['Content-Type' => 'application/json; charset=utf-8'],
                $expectedResponse
            )
        );

        $response = $this->skynet->quote([
            'collect-city' => 'Brackenfell',
            'deliver-city' => 'Stellenbosch',
            'service-type' => 'DDV',
            'insurance-type' => '1',
            'parcel-insurance' => '0',
            'deliver-postcode' => '7600',
            'parcel-length' => 70,
            'parcel-width' => 80,
            'parcel-height' => 90,
            'parcel-weight' => 60,
        ]);

        $this->assertEquals(true, $response->successful());
        $this->assertEquals($expectedResponse, $response->body());
        $this->assertEquals('application/json; charset=utf-8', $response->header('Content-Type'));
    }

    /** @test */
    public function it_can_get_an_ETA_between_two_locations()
    {
        $expectedResponse = '{
                                "errorDescription": "",
                                "errorCode": 0,
                                "FromBranch": "CPT",
                                "FromClassification": "L2",
                                "responsibleFromBranch": "CPT",
                                "responsibleFromBranchCity": "CAPE TOWN",
                                "responsibleFromBranchCountry": "SOUTH AFRICA",
                                "FromRouting": "BR6",
                                "ToBranch": "CPT",
                                "ToClassification": "L3",
                                "responsibleToBranch": "CPT",
                                "responsibleToBranchCity": "STELLENBOSCH",
                                "responsibleToBranchCountry": "SOUTH AFRICA",
                                "ToRouting": "STEL",
                                "skyQWaybillETAs": [
                                    {
                                        "estimatedDeliveryDate": "23/6/2021 13:00:00",
                                        "serviceType": "ON1"
                                    }
                                ]
                            }';
        $this->mock->append(
            new Response(
                200,
                ['Content-Type' => 'application/json; charset=utf-8'],
                "{\"SecurityToken\":\"2_03ce4988-43db-45ea-9797-e69befff8d3f\"}"
            )
        );
        $this->mock->append(
            new Response(
                200,
                ['Content-Type' => 'application/json; charset=utf-8'],
                $expectedResponse
            )
        );

        $response = $this->skynet->deliveryETA([
            'from-suburb' => 'Brackenfell',
            'from-postcode' => '7560',
            'to-suburb' => 'Stellenbosch',
            'to-postcode' => '7600',
            'service-type' => 'ON1',
        ]);

        $this->assertEquals(true, $response->successful());
        $this->assertEquals($expectedResponse, $response->body());
        $this->assertEquals('application/json; charset=utf-8', $response->header('Content-Type'));
    }

    /** @test */
    public function it_can_create_a_waybill()
    {
        $expectedResponse = '{
                                "errorDescription": "",
                                "errorCode": 0,
                                "waybillNumber": "080900028413",
                                "collectionReferenceNumber": null
                            }';
        $this->mock->append(
            new Response(
                200,
                ['Content-Type' => 'application/json; charset=utf-8'],
                "{\"SecurityToken\":\"2_03ce4988-43db-45ea-9797-e69befff8d3f\"}"
            )
        );
        $this->mock->append(
            new Response(
                200,
                ['Content-Type' => 'application/json; charset=utf-8'],
                $expectedResponse
            )
        );

        $response = $this->skynet->createWaybill([
            "customer-reference" => "Customer Reference",
            "GenerateWaybillNumber" => true,
            "service-type" => "DDV",
            "collection-date" => "2021-06-26",
            "from-address-1" => "3 Janie Street, Ferndale, Brackenfell",
            "from-suburb" => "Brackenfell",
            "from-postcode" => "7560",
            "to-address-1" => "15 Verreweide Street, Universiteitsoord, Stellenbosch",
            "to-suburb" => "Stellenbosch",
            "to-postcode" => "7600",
            "insurance-type" => "1",
            "insurance-amount" => "0",
            "security" => "N",
            "parcel-number" => "1",
            "parcel-length" => 10,
            "parcel-width" => 20,
            "parcel-height" => 30,
            "parcel-weight" => 10,
            "parcel-reference" => "12345",
            "offsite-collection" => true,
        ]);

        $this->assertEquals(true, $response->successful());
        $this->assertEquals($expectedResponse, $response->body());
        $this->assertEquals('application/json; charset=utf-8', $response->header('Content-Type'));
    }

    /** @test */
    public function it_can_return_a_waybill_POD_image()
    {
        $expectedResponse = '{
                                "image": null,
                                "imageType": "pdf",
                                "errorDescription": "Success",
                                "errorCode": 0
                            }';
        $this->mock->append(
            new Response(
                200,
                ['Content-Type' => 'application/json; charset=utf-8'],
                "{\"SecurityToken\":\"2_03ce4988-43db-45ea-9797-e69befff8d3f\"}"
            )
        );
        $this->mock->append(
            new Response(
                200,
                ['Content-Type' => 'application/json; charset=utf-8'],
                $expectedResponse
            )
        );

        $response = $this->skynet->waybillPOD('080900028436');

        $this->assertEquals(true, $response->successful());
        $this->assertEquals($expectedResponse, $response->body());
        $this->assertEquals('application/json; charset=utf-8', $response->header('Content-Type'));
    }

    /** @test */
    public function it_can_return_tracking_information_for_a_waybill()
    {
        $expectedResponse = '[
                                {
                                    "WaybillNumber": "080900028413",
                                    "ReferenceList": [
                                        "CUSTOMERREFERENCE"
                                    ],
                                    "TrackingInfo": [
                                        {
                                            "WaybillEventDate": "22/06/2021",
                                            "WaybillEventTime": "15:51:02",
                                            "WaybillEventDescription": "Created waybill",
                                            "WaybillEventBranch": "HostServer",
                                            "WaybillEventId": 148593792
                                        }
                                    ],
                                    "PODDetails": []
                                }
                            ]';

        $this->mock->append(
            new Response(
                200,
                ['Content-Type' => 'application/json; charset=utf-8'],
                $expectedResponse
            )
        );

        $response = $this->skynet->trackWaybill('080900028413');

        $this->assertEquals(true, $response->successful());
        $this->assertEquals($expectedResponse, $response->body());
        $this->assertEquals('application/json; charset=utf-8', $response->header('Content-Type'));
    }

    /** @test */
    public function it_throws_a_request_exception()
    {
        $this->mock->append(
            new RequestException(
                'Error communicating with the server',
                new Request('POST', 'testURI')
            )
        );

        $this->expectException(RequestException::class);

        $response = $this->skynet->securityToken();
    }

    /** @test */
    public function it_throws_a_client_exception()
    {
        $this->mock->append(
            new ClientException(
                'Client error',
                new Request('POST', 'testURI'),
                new Response(
                    400,
                    ['Content-Type' => 'application/json; charset=utf-8'],
                    "Bad Request."
                )
            )
        );

        $this->expectException(ClientException::class);

        $response = $this->skynet->securityToken();
    }
}
