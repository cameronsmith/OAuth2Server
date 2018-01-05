<?php namespace App\Controllers;

use App\Repositories\UserRepository;
use App\Repositories\RefreshTokenRepository;
use League\OAuth2\Server\Grant\PasswordGrant;

class PasswordController extends ApiController
{
    /**
     * Authorize a user with a password grant.
     *
     * @return \Psr\Http\Message\StreamInterface
     */
    public function authorize() {
        $grant = new PasswordGrant(
            new UserRepository(),           // instance of UserRepositoryInterface
            new RefreshTokenRepository()    // instance of RefreshTokenRepositoryInterface
        );

        $grant->setRefreshTokenTTL(new \DateInterval('P1M'));

        $this->authorizationServer->enableGrantType(
            $grant,
            new \DateInterval('PT1H') // access tokens will expire after 1 hour
        );

        return $this->respondJson($this->authorizationServer->respondToAccessTokenRequest(
            $this->request,
            $this->response
        ));
    }
}