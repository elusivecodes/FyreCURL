<?php
declare(strict_types=1);

namespace Tests;

use
    Fyre\CURL\Curl,
    PHPUnit\Framework\TestCase;

use function
    exec,
    json_encode,
    sleep,
    strlen;

final class CurlTest extends TestCase
{

    protected static int $pid;

    public function testDeleteMethod(): void
    {
        $response = Curl::delete('localhost:8888/method');

        $this->assertSame(
            'DELETE',
            $response->getBody()
        );
    }

    public function testGetMethod(): void
    {
        $response = Curl::get('localhost:8888/method');

        $this->assertSame(
            'GET',
            $response->getBody()
        );
    }

    public function testGetData(): void
    {
        $response = Curl::get('localhost:8888/get', [
            'data' => [
                'value' => 1
            ]
        ]);

        $this->assertSame(
            '{"value":"1"}',
            $response->getBody()
        );
    }

    public function testHeadMethod(): void
    {
        $response = Curl::head('localhost:8888/method');

        $this->assertSame(
            '',
            $response->getBody()
        );
    }

    public function testOptionsMethod(): void
    {
        $response = Curl::options('localhost:8888/method');

        $this->assertSame(
            'OPTIONS',
            $response->getBody()
        );
    }

    public function testPatchMethod(): void
    {
        $response = Curl::patch('localhost:8888/method');

        $this->assertSame(
            'PATCH',
            $response->getBody()
        );
    }

    public function testPatchData(): void
    {
        $response = Curl::patch('localhost:8888/json', [
            'value' => 1
        ]);

        $this->assertSame(
            '{"value":1}',
            $response->getBody()
        );
    }

    public function testPostMethod(): void
    {
        $response = Curl::post('localhost:8888/method');

        $this->assertSame(
            'POST',
            $response->getBody()
        );
    }

    public function testPostData(): void
    {
        $response = Curl::post('localhost:8888/post', [
            'value' => 1
        ]);

        $this->assertSame(
            '{"value":"1"}',
            $response->getBody()
        );
    }

    public function testPutMethod(): void
    {
        $response = Curl::put('localhost:8888/method');

        $this->assertSame(
            'PUT',
            $response->getBody()
        );
    }

    public function testPutData(): void
    {
        $response = Curl::put('localhost:8888/json', [
            'value' => 1
        ]);

        $this->assertSame(
            '{"value":1}',
            $response->getBody()
        );
    }

    public function testAuth(): void
    {
        $response = Curl::get('localhost:8888/auth', [
            'username' => 'test',
            'password' => 'password'
        ]);

        $this->assertSame(
            '{"user":"test","password":"password"}',
            $response->getBody()
        );
    }

    public function testAgent(): void
    {
        $response = Curl::get('localhost:8888/agent', [
            'userAgent' => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.2704.103 Safari/537.36'
        ]);

        $this->assertSame(
            'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.2704.103 Safari/537.36',
            $response->getBody()
        );
    }

    public function testHeader(): void
    {
        $response = Curl::get('localhost:8888/header', [
            'headers' => [
                'Accept' => 'text/html'
            ]
        ]);

        $this->assertSame(
            'text/html',
            $response->getBody()
        );
    }

    public function testProtocolVersion(): void
    {
        $response = Curl::get('localhost:8888/version', [
            'protocolVersion' => '1.0'
        ]);

        $this->assertSame(
            'HTTP/1.0',
            $response->getBody()
        );
    }

    public function testProcessData(): void
    {
        $data = json_encode(['value' => 1]);
        $length = strlen($data);

        $response = Curl::post('localhost:8888/json', [], [
            'data' => $data,
            'headers' => [
                'Content-Type' => 'application/json',
                'Content-Length' => (string) $length
            ],
            'processData' => false
        ]);

        $this->assertSame(
            '{"value":1}',
            $response->getBody()
        );
    }

    public static function setUpBeforeClass(): void
    {
        static::$pid = (int) exec('nohup php -S localhost:8888 tests/Mock/server.php >/dev/null 2>&1 & echo $!');
        sleep(1);
    }

    public static function tearDownAfterClass(): void
    {
        exec('kill '.static::$pid);
    }

}
