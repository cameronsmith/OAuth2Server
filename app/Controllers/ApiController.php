<?php namespace App\Controllers;

use League\OAuth2\Server\AuthorizationServer;
use App\Repositories\ClientRepository;
use App\Repositories\AccessTokenRepository;
use App\Repositories\ScopeRepository;
use Slim\Http\Response;
use Slim\Http\Request;

class ApiController
{
    protected $authorizationServer;
    protected $request;
    protected $response;

    /**
     * ApiController constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->response = new Response;

        $this->authorizationServer = new AuthorizationServer(
            new ClientRepository(),                 // instance of ClientRepositoryInterface
            new AccessTokenRepository(),            // instance of AccessTokenRepositoryInterface
            new ScopeRepository(),                  // instance of ScopeRepositoryInterface
            'file://' . __DIR__ . '/../../storage/private.key',    // path to private key
            getenv('ENCRYPTION_KEY')      // encryption key
        );
    }

    /**
     * Return JSON response.
     *
     * @param Response $response
     * @return \Psr\Http\Message\StreamInterface
     */
    protected function respondJson(Response $response) {
        header('pragma: no-cache');
        header('cache-control: no-store');
        header('content-type: application/json; charset=UTF-8');
        return $response->getBody();
    }
}