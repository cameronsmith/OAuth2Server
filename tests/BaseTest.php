<?php namespace Tests;

use PHPUnit\Framework\TestCase;
use Tests\CreateApplication;
use Slim\Http\Request;
use Tests\Stubs\PhpStream;
use App\Repositories\RepositoryConnection;
use App\Helpers\Path;

abstract class BaseTest extends TestCase
{
    use CreateApplication;

    const POST = 'POST';
    const GET = 'GET';

    protected $app;
    protected $repoConnection;

    /**
     * Setup test application.
     */
    public function setUp() {
        parent::setUp();

        $this->runMigrations();
        $this->app = $this->getAppInstance();
        $this->repoConnection = $this->makeRepoConnection();
    }

    /**
     * Make a post request to the application.
     *
     * @param $uri
     * @param array $body
     * @param array $headers
     * @return mixed
     */
    public function post($uri, $body = [], $headers = []) {
        $this->createRequest(self::POST, $body, $uri, $headers);
        return json_decode($this->app->run(), true);
    }

    /**
     * Make a get request to the application.
     *
     * @param $uri
     * @param $body
     * @param array $headers
     * @return mixed
     */
    public function get($uri, $body, $headers = []) {

        $request = $this->arrayToUrlRequest($body);
        $uri .= $request;
        $this->createRequest(self::GET, [], $uri, $headers);
        $response = $this->app->run();
        $jsonResponse = json_decode($response, true);

        return (json_last_error() == JSON_ERROR_NONE ? $jsonResponse : $response);
    }

    /**
     * Phrase an array to a url request.
     *
     * @param array $array
     * @return string
     */
    protected function arrayToUrlRequest(array $array) {
        $requestVariables = [];
        foreach($array as $key => $value) {
            $requestVariables[] = "$key=$value";
        }

        return '?' . implode('&', $requestVariables);
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
    protected function createRequest($method, $body, $uri, $headers = []) {
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

    /**
     * Make a repo connection.
     *
     * @return RepositoryConnection
     */
    protected function makeRepoConnection() {
        return RepositoryConnection::getConnectionInstance([
            'file' => Path::getStoragePath() . DIRECTORY_SEPARATOR . getenv('DB_FILE')
        ]);
    }

    /**
     * From a factory persist.
     *
     * @param $factory
     * @param array $data
     * @return mixed
     */
    protected function factory($factory, array $data = []) {
        return $factory::create($data)->persist($this->repoConnection);
    }
}