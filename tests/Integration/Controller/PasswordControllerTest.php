<?php

use Tests\BaseTest;
use App\Factories\ClientFactory;
use App\Factories\UserFactory;
use App\Factories\ScopeFactory;

class PasswordControllerTest extends BaseTest
{
    const AUTH_ENDPOINT = '/password';

    protected $validPost = [];

    public function setUp()
    {
        parent::setUp();

        $this->factory(ScopeFactory::class);

        $this->factory(ClientFactory::class, [
            'secret' => password_hash('secret1!', PASSWORD_BCRYPT),
        ]);

        $this->factory(UserFactory::class, [
            'username' => 'user1',
            'password' => password_hash('Password1!', PASSWORD_BCRYPT),
        ]);

        $this->validPost = [
            'grant_type' => 'password',
            'client_id' => 1,
            'client_secret' => 'secret1!',
            'username' => 'user1',
            'password' => 'Password1!'
        ];
    }

    public function testAUserCanAuthenticate()
    {
        $response = $this->post(self::AUTH_ENDPOINT, $this->validPost);

        $this->assertTrue($response['token_type'] === 'Bearer');
        $this->assertTrue(!empty($response['access_token']));
        $this->assertTrue(!empty($response['refresh_token']));
    }

    public function testAUserWithTheWrongPasswordCannotAuthenticate()
    {
        $post = $this->validPost;
        $post['password'] = 'dontknow';

        $response = $this->post(self::AUTH_ENDPOINT, $post);

        $this->assertTrue(strtoupper($response['error']) === 'UNAUTHORIZED');;
        $this->assertArrayNotHasKey('access_token', $response);
    }

    public function testAUserWithTheWrongClientSecretCannotAuthenticate()
    {
        $post = $this->validPost;
        $post['client_secret'] = 'dontknow';

        $response = $this->post(self::AUTH_ENDPOINT, $post);

        $this->assertTrue(strtoupper($response['error']) === 'UNAUTHORIZED');;
        $this->assertArrayNotHasKey('access_token', $response);
    }
}