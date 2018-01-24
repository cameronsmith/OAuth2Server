<?php namespace App\Controllers;

use League\OAuth2\Server\Grant\ImplicitGrant;
use App\Entities\UserEntity;
use League\OAuth2\Server\Exception\OAuthServerException;

class ImplicitController extends ApiController
{
    /**
     * Authorize a user + client with a password grant .
     *
     * @return \Psr\Http\Message\StreamInterface|string
     */
    public function authorize() {
        $authorizationServer = $this->getAuthorizationServer();

        $authorizationServer->enableGrantType(
            new ImplicitGrant(new \DateInterval(getenv('ACCESS_TOKEN_EXPIRE')))
        );

        try {
            $authRequest = $authorizationServer->validateAuthorizationRequest($this->request);

            $authRequest->setUser(new UserEntity());

            $authRequest->setAuthorizationApproved(true);

            return $authorizationServer->completeAuthorizationRequest($authRequest, $this->response);
        } catch (OAuthServerException $exception) {
            return $this->respondUnauthorized();
        }
    }
}