<?php namespace Tests;

use PHPUnit\Framework\TestCase;
use Tests\CreateApplication;
use Slim\Http\Request;
use Tests\Stubs\PhpStream;

abstract class BaseTest extends TestCase
{
    use CreateApplication;

    const POST = 'POST';

    protected $app;

    /**
     * Setup test application.
     */
    public function setUp() {
        parent::setUp();

        $this->runMigrations();
        $this->app = $this->getAppInstance();
    }

    /**
     * Make a post request to the application.
     *
     * @param $uri
     * @param string $body
     * @param array $headers
     * @return mixed
     */
    public function post($uri, $body = '', $headers = []) {
        $this->createRequest(self::POST, $body, $uri, $headers);
        return $this->app->run();
    }

    /**
     * Create a request.
     *
     * This will override the original request held in the app.
     *
     * @param $method
     * @param array $body
     * @param $uri
     * @param array $headers
     */
    protected function createRequest($method, array $body, $uri, $headers = []) {
        $request = [
            'REQUEST_METHOD' => $method,
            'REQUEST_URI' => $uri,
            'HTTP_CONTENT_TYPE' => (count($headers) > 0 ? $headers : 'application/json')
        ];

        $this->attachRequestToSingleton($request, $body);
    }

    /**
     * Attach a request to the app singleton method.
     *
     * @param $request
     * @param $body
     */
    protected function attachRequestToSingleton($request, $body) {
        $this->mockPhpStream();
        file_put_contents('php://input', json_encode($body));
        $this->app->bindSingleton(Request::createFromGlobals($request));
        $this->unmockPhpStream();
    }

    /**
     * Mock php streams.
     */
    protected function mockPhpStream() {
        stream_wrapper_unregister("php");
        stream_wrapper_register("php", PhpStream::class);
    }

    /**
     * Re-register original PHP streams.
     */
    protected function unmockPhpStream() {
        stream_wrapper_restore("php");
    }
}