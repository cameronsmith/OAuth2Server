<?php

use Tests\BaseTest;
use App\Factories\ClientFactory;
use App\Factories\UserFactory;

class ClientControllerTest extends BaseTest
{
    const CLIENT_ENDPOINT = '/client';

    protected $validPost = [
        'grant_type' => 'client_credentials',
        'client_id' => 1,
        'client_secret' => 'secret1!',
        'scope' => 'email',
    ];

    public function setUp()
    {
        parent::setUp();

        $this->factory(ClientFactory::class, [
            'secret' => password_hash('secret1!', PASSWORD_BCRYPT),
        ]);
    }

    public function testAClientCanAuthenticate()
    {
        $response = $this->post(self::CLIENT_ENDPOINT, $this->validPost);

        $this->assertTrue($response['token_type'] === 'Bearer');
        $this->assertTrue(!empty($response['access_token']));
        $this->assertTrue(empty($response['refresh_token']));
    }

    public function testAClientWithTheWrongClientIdCannotAuthenticate()
    {
        $post = $this->validPost;
        $post['client_id'] = '9';

        $response = $this->post(self::CLIENT_ENDPOINT, $post);

        $this->assertTrue(strtoupper($response['error']) === 'UNAUTHORIZED');;
        $this->assertArrayNotHasKey('access_token', $response);
    }

    public function testAClientWithTheWrongClientSecretCannotAuthenticate()
    {
        $post = $this->validPost;
        $post['client_secret'] = 'dontknow';

        $response = $this->post(self::CLIENT_ENDPOINT, $post);

        $this->assertTrue(strtoupper($response['error']) === 'UNAUTHORIZED');;
        $this->assertArrayNotHasKey('access_token', $response);
    }
}