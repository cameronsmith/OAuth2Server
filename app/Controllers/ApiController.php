<?php namespace App\Controllers;

use League\OAuth2\Server\AuthorizationServer;
use App\Repositories\ClientRepository;
use App\Repositories\AccessTokenRepository;
use App\Repositories\ScopeRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface  as Request;
use App\Repositories\RepositoryConnection;
use App\Helpers\HttpCodes;
use App\Helpers\Path;

class ApiController
{
    protected $request;
    protected $response;
    protected $repoConnection;

    /**
     * ApiController constructor.
     *
     * @param Request $request
     * @param Response $response
     * @param RepositoryConnection $repoConnection
     */
    public function __construct(Request $request, Response $response, RepositoryConnection $repoConnection)
    {
        $this->request = $request;
        $this->repoConnection = $repoConnection;
        $this->response = $response;
    }

    /**
     * Get an authorization server.
     *
     * @return bool|AuthorizationServer
     */
    public function getAuthorizationServer() {

        $authorizationServer = new AuthorizationServer(
            new ClientRepository($this->repoConnection),
            new AccessTokenRepository(),
            new ScopeRepository(),
            'file://' . Path::getStoragePath() . '/private.key',
            getenv('ENCRYPTION_KEY')
        );

        return $authorizationServer;
    }

    /**
     * Send JSON headers.
     */
    protected function sendJsonHeader() {
        header('pragma: no-cache');
        header('cache-control: no-store');
        header('content-type: application/json; charset=UTF-8');
    }

    /**
     * Return JSON OK response.
     *
     * @param Response $response
     * @param int $httpCode
     * @return \Psr\Http\Message\StreamInterface
     */
    protected function respondOk(Response $response, $httpCode = HttpCodes::HTTP_OK) {
        http_response_code($httpCode);
        $this->sendJsonHeader();
        return $response->getBody();
    }

    /**
     * Return JSON unauthorized response.
     *
     * @param int $httpCode
     * @return string
     */
    protected function respondUnauthorized($httpCode = HttpCodes::HTTP_UNAUTHORIZED) {
        http_response_code($httpCode);
        $this->sendJsonHeader();
        return json_encode([
            'error' => 'Unauthorized',
            'http_code' => $httpCode,
        ]);
    }

    /**
     * Return JSON internal error response.
     *
     * @param int $httpCode
     * @return string
     */
    protected function respondInternalError($httpCode = HttpCodes::HTTP_INTERNAL_SERVER_ERROR) {
        http_response_code($httpCode);
        $this->sendJsonHeader();
        return json_encode([
            'error' => 'Internal error',
            'http_code' => $httpCode,
        ]);
    }
}