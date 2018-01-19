<?php

use Tests\BaseTest;
use App\Factories\ClientFactory;
use App\Factories\UserFactory;
use App\Helpers\HttpCodes;

class RefreshControllerTest extends BaseTest
{
    const AUTH_ENDPOINT = '/password';
    const REFRESH_ENDPOINT = '/refresh';

    protected $authPost = [];
    protected $refreshPost = [];

    public function setUp()
    {
        parent::setUp();

        $this->factory(ClientFactory::class, [
            'secret' => password_hash('secret1!', PASSWORD_BCRYPT),
        ]);

        $this->factory(UserFactory::class, [
            'username' => 'user1',
            'password' => password_hash('Password1!', PASSWORD_BCRYPT),
        ]);

        $this->authPost = [
            'grant_type' => 'password',
            'client_id' => 1,
            'client_secret' => 'secret1!',
            'username' => 'user1',
            'password' => 'Password1!'
        ];

        $this->refreshPost = [
            'grant_type' => 'refresh_token',
            'client_id' => 1,
            'client_secret' => 'secret1!',
        ];
    }

    public function testAUserCanRefreshFromAnAuthentication()
    {
        $response = $this->post(self::AUTH_ENDPOINT, $this->authPost);
        $this->assertTrue(!empty($response['refresh_token']));

        $this->refreshPost['refresh_token'] = $response['refresh_token'];

        $response = $this->post(self::REFRESH_ENDPOINT, $this->refreshPost);

        $this->assertTrue($response['token_type'] === 'Bearer');
        $this->assertTrue(!empty($response['access_token']));
        $this->assertTrue(!empty($response['refresh_token']));
    }

    public function testAUserWithInvalidClientCannotAuthenticate()
    {
        $response = $this->post(self::AUTH_ENDPOINT, $this->authPost);
        $this->assertTrue(!empty($response['refresh_token']));

        $this->refreshPost['refresh_token'] = $response['refresh_token'];
        $this->refreshPost['client_secret'] = 'noreal';
        $response = $this->post(self::REFRESH_ENDPOINT, $this->refreshPost);

        $this->assertTrue(strtoupper($response['error']) === 'UNAUTHORIZED');
        $this->assertTrue(strtoupper($response['http_code']) == HttpCodes::HTTP_UNAUTHORIZED);
    }

    public function testAUserWithAnExpiredRefreshTokenCannotRefresh()
    {
        $response = $this->post(self::AUTH_ENDPOINT, $this->authPost);
        $this->assertTrue(!empty($response['refresh_token']));

        $date = new DateTime();
        $date->sub(new \DateInterval(getenv('REFRESH_TOKEN_EXPIRE')));
        $date->sub(new \DateInterval(getenv('REFRESH_TOKEN_EXPIRE')));

        $statement = $this->repoConnection->prepare('UPDATE refresh_tokens SET expires=:expires');
        $statement->bindValue('expires', $date->format('Y-m-d H:i:s'));
        $statement->execute();

        $this->refreshPost['refresh_token'] = $response['refresh_token'];
        $response = $this->post(self::REFRESH_ENDPOINT, $this->refreshPost);

        $this->assertTrue(strtoupper($response['error']) === 'UNAUTHORIZED');
        $this->assertTrue(strtoupper($response['http_code']) == HttpCodes::HTTP_UNAUTHORIZED);
    }

    public function testARevokedRefreshTokenWillNotRefresh()
    {
        $response = $this->post(self::AUTH_ENDPOINT, $this->authPost);
        $this->assertTrue(!empty($response['refresh_token']));

        $statement = $this->repoConnection->prepare('UPDATE refresh_tokens SET revoked=:revoked');
        $statement->bindValue('revoked', 1);
        $statement->execute();

        $this->refreshPost['refresh_token'] = $response['refresh_token'];
        $response = $this->post(self::REFRESH_ENDPOINT, $this->refreshPost);

        $this->assertTrue(strtoupper($response['error']) === 'UNAUTHORIZED');
        $this->assertTrue(strtoupper($response['http_code']) == HttpCodes::HTTP_UNAUTHORIZED);
    }
}