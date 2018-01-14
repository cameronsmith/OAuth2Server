<?php namespace App\Controllers;

use App\Repositories\UserRepository;
use App\Repositories\RefreshTokenRepository;
use League\OAuth2\Server\Grant\PasswordGrant;
use League\OAuth2\Server\Exception\OAuthServerException;
use Exception;
use App\Helpers\HttpCodes;

class PasswordController extends ApiController
{
    /**
     * Authorize a user + client with a password grant .
     *
     * @return \Psr\Http\Message\StreamInterface|string
     */
    public function authorize() {
        $grant = new PasswordGrant(
            new UserRepository($this->repoConnection),
            new RefreshTokenRepository()
        );

        $grant->setRefreshTokenTTL(new \DateInterval('P1M'));

        $authorizationServer = $this->getAuthorizationServer();
        if (!$authorizationServer) {
            return $this->respondUnauthorized();
        }

        $authorizationServer->enableGrantType(
            $grant,
            new \DateInterval('PT1H') // access tokens will expire after 1 hour
        );

        try {
            return $this->respondOk($authorizationServer->respondToAccessTokenRequest(
                $this->request,
                $this->response
            ));
        } catch (OAuthServerException $exception) {
            return $this->respondUnauthorized();
        }
    }
}