<?php


namespace Claassenmarius\PhpSkynet\Tests;


use Claassenmarius\PhpSkynet\Skynet;
use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;

class SkynetTest extends TestCase
{

    private $skynet;

    protected function setUp(): void
    {
        $this->skynet =  new Skynet(
            'iDeliverTest',
            '!D3liver1!',
            '2',
            'J99133'
        );
    }

    /** @test */
    public function it_asserts_true()
    {
        $this->assertTrue(true);
    }


    /** @test */
    public function it_can_return_a_security_code()
    {
        $response = $this->skynet->securityToken();

        $this->assertArrayHasKey('SecurityToken', $response->json());
    }

    /** @test */
    public function it_validates_suburb_and_post_code()
    {
        $response = $this->skynet->validateSuburbAndPostalCode([
            'suburb' => 'Brackenfell',
            'postal-code' => 7560
        ]);

        $this->assertEquals('true', $response->body());
    }

    /** @test */
    public function it_can_get_postal_codes_from_suburb()
    {
        $response = $this->skynet->postalCodesFromSuburb('Brackenfell');

        $this->assertArrayHasKey('postalCodeId', ($response->json())[0]);
    }
}