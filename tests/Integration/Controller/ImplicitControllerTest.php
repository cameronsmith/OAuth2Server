<?php

use Tests\BaseTest;
use App\Factories\ClientFactory;
use App\Factories\UserFactory;
use App\Helpers\HttpCodes;

class ImplicitControllerTest extends BaseTest
{
    const IMPLICIT_ENDPOINT = '/implicit';

    protected $implicitCodeGet;

    public function setUp()
    {
        parent::setUp();

        $this->factory(ClientFactory::class, [
            'secret' => password_hash('secret1!', PASSWORD_BCRYPT),
        ]);

        $this->implicitCodeGet = [
            'response_type' => 'token',
            'client_id' => '1',
            'redirect_uri' => '/',
            'scope' => 'email',
        ];
    }

    public function testAUserCanGetARequestAuthenticationCode()
    {
        $response = $this->get(self::IMPLICIT_ENDPOINT, $this->implicitCodeGet);
        $positionOfCodeVariable = stripos($response, 'access_token=');
        $this->assertTrue($positionOfCodeVariable !== false);
    }

    public function testAUserWithAnInvalidClientIdCannotRequestAuthenticationCode()
    {
        $get = $this->implicitCodeGet;
        $get['client_id'] = '9';

        $response = $this->get(self::IMPLICIT_ENDPOINT, $get);
        $this->assertTrue(strtoupper($response['error']) === 'UNAUTHORIZED');;
        $this->assertArrayNotHasKey('access_token', $response);
    }
}