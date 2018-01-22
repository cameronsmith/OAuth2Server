<?php

use Tests\BaseTest;
use App\Factories\ClientFactory;
use App\Factories\UserFactory;
use App\Helpers\HttpCodes;

class AuthCodeControllerTest extends BaseTest
{
    const AUTH_CODE_ENDPOINT = '/auth-code';

    protected $authCodePost;
    protected $authCodeGet;

    public function setUp()
    {
        parent::setUp();

        $this->factory(ClientFactory::class, [
            'secret' => password_hash('secret1!', PASSWORD_BCRYPT),
        ]);

        $this->authCodeGet = [
            'response_type' => 'code',
            'client_id' => '1',
            'redirect_uri' => '/',
            'scope' => 'email',
        ];

        $this->authCodePost = [
            'grant_type' => 'authorization_code',
            'client_id' => '1',
            'client_secret' => 'secret1!',
            'redirect_uri' => '/',
            'code' => '',
        ];
    }

    public function testAUserCanGetARequestAuthenticationCode()
    {
        $response = $this->get(self::AUTH_CODE_ENDPOINT, $this->authCodeGet);
        $positionOfCodeVariable = stripos($response, 'code=');
        $this->assertTrue($positionOfCodeVariable !== false);
        $authorizationCode = trim(substr($response, ($positionOfCodeVariable + 5)));

        $post = $this->authCodePost;
        $post['code'] = $authorizationCode;

        $response = $this->post(self::AUTH_CODE_ENDPOINT, $post);

        $this->assertTrue($response['token_type'] === 'Bearer');
        $this->assertTrue(!empty($response['access_token']));
        $this->assertTrue(!empty($response['refresh_token']));
    }

    public function testAUserWithAnInvalidClientIdCannotRequestAuthenticationCode()
    {
        $get = $this->authCodeGet;
        $get['client_id'] = '9';

        $response = $this->get(self::AUTH_CODE_ENDPOINT, $get);
        $this->assertTrue(strtoupper($response['error']) === 'UNAUTHORIZED');;
        $this->assertArrayNotHasKey('access_token', $response);
    }

    public function testAUserWithAnInvalidCodeCannotRequestAToken()
    {
        $post = $this->authCodePost;
        $post['code'] = '123';

        $response = $this->post(self::AUTH_CODE_ENDPOINT, $post);
        $this->assertTrue(strtoupper($response['error']) === 'UNAUTHORIZED');;
        $this->assertArrayNotHasKey('access_token', $response);
    }

    public function testAUserWithAnExpiredCodeCannotRequestAToken() {
        $response = $this->get(self::AUTH_CODE_ENDPOINT, $this->authCodeGet);
        $positionOfCodeVariable = stripos($response, 'code=');
        $this->assertTrue($positionOfCodeVariable !== false);
        $authorizationCode = trim(substr($response, ($positionOfCodeVariable + 5)));

        $post = $this->authCodePost;
        $post['code'] = $authorizationCode;

        $date = new DateTime();
        $date->sub(new \DateInterval(getenv('AUTH_CODE_EXPIRE')));
        $date->sub(new \DateInterval(getenv('AUTH_CODE_EXPIRE')));

        $statement = $this->repoConnection->prepare('UPDATE auth_tokens SET expires=:expires');
        $statement->bindValue('expires', $date->format('Y-m-d H:i:s'));
        $statement->execute();

        $response = $this->post(self::AUTH_CODE_ENDPOINT, $post);
        $this->assertTrue(strtoupper($response['error']) === 'UNAUTHORIZED');;
        $this->assertArrayNotHasKey('access_token', $response);
    }

    public function testAUserWithARevokedCodeCannotRequestAToken() {
        $response = $this->get(self::AUTH_CODE_ENDPOINT, $this->authCodeGet);
        $positionOfCodeVariable = stripos($response, 'code=');
        $this->assertTrue($positionOfCodeVariable !== false);
        $authorizationCode = trim(substr($response, ($positionOfCodeVariable + 5)));

        $post = $this->authCodePost;
        $post['code'] = $authorizationCode;

        $statement = $this->repoConnection->prepare('UPDATE auth_tokens SET revoked=:revoked');
        $statement->bindValue('revoked', 1);
        $statement->execute();

        $response = $this->post(self::AUTH_CODE_ENDPOINT, $post);
        $this->assertTrue(strtoupper($response['error']) === 'UNAUTHORIZED');;
        $this->assertArrayNotHasKey('access_token', $response);
    }
}