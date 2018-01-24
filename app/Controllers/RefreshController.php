<?php namespace App\Controllers;

use App\Repositories\UserRepository;
use App\Repositories\RefreshTokenRepository;
use League\OAuth2\Server\Grant\RefreshTokenGrant;
use League\OAuth2\Server\Exception\OAuthServerException;
use Exception;
use App\Helpers\HttpCodes;

class RefreshController extends ApiController
{

    /**
     * Issue a new auth response with refresh if the client + refresh token is valid.
     *
     * @return \Psr\Http\Message\StreamInterface|string
     */
    public function authorize() {
        $grant = new RefreshTokenGrant(
            new RefreshTokenRepository($this->repoConnection)
        );

        $grant->setRefreshTokenTTL(new \DateInterval(getenv('REFRESH_TOKEN_EXPIRE')));

        $authorizationServer = $this->getAuthorizationServer();
        if (!$authorizationServer) {
            return $this->respondUnauthorized();
        }

        $authorizationServer->enableGrantType(
            $grant,
            new \DateInterval(getenv('ACCESS_TOKEN_EXPIRE')) // access tokens will expire after 1 hour
        );

        //try {
            return $this->respondOk($authorizationServer->respondToAccessTokenRequest(
                $this->request,
                $this->response
            ));
        //} catch (OAuthServerException $exception) {
            //return $this->respondUnauthorized();
        //}
    }
}