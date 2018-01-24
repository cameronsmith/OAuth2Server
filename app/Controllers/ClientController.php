<?php namespace App\Controllers;

use League\OAuth2\Server\Exception\OAuthServerException;
use \League\OAuth2\Server\Grant\ClientCredentialsGrant;


class ClientController extends ApiController
{
    public function authorize()
    {
        $authorizationServer = $this->getAuthorizationServer();

        $authorizationServer->enableGrantType(
            new ClientCredentialsGrant,
            new \DateInterval(getenv('ACCESS_TOKEN_EXPIRE')) // access tokens will expire after 1 hour
        );

        try {
            return $this->respondOk($authorizationServer->respondToAccessTokenRequest($this->request, $this->response));
        } catch (OAuthServerException $exception) {
            return $this->respondUnauthorized();
        }
    }
}