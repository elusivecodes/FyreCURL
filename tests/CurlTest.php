<?php
declare(strict_types=1);

namespace Tests;

use Fyre\CURL\Curl;
use Fyre\CURL\CurlResponse;
use Fyre\Utility\Traits\MacroTrait;
use PHPUnit\Framework\TestCase;

use function class_uses;
use function exec;
use function json_encode;
use function sleep;
use function strlen;

final class CurlTest extends TestCase
{
    protected static int $pid;

    public function testAgent(): void
    {
        $response = Curl::get('localhost:8888/agent', [
            'userAgent' => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.2704.103 Safari/537.36',
        ]);

        $this->assertSame(
            'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.2704.103 Safari/537.36',
            $response->getBody()->getContents()
        );
    }

    public function testAuth(): void
    {
        $response = Curl::get('localhost:8888/auth', [
            'username' => 'test',
            'password' => 'password',
        ]);

        $this->assertSame(
            [
                'username' => 'test',
                'password' => 'password',
            ],
            $response->getJson()
        );
    }

    public function testDeleteMethod(): void
    {
        $response = Curl::delete('localhost:8888/method');

        $this->assertSame(
            'DELETE',
            $response->getBody()->getContents()
        );
    }

    public function testGetData(): void
    {
        $response = Curl::get('localhost:8888/get', [
            'data' => [
                'value' => 1,
            ],
        ]);

        $this->assertSame(
            [
                'value' => '1',
            ],
            $response->getJson()
        );
    }

    public function testGetMethod(): void
    {
        $response = Curl::get('localhost:8888/method');

        $this->assertSame(
            'GET',
            $response->getBody()->getContents()
        );
    }

    public function testHeader(): void
    {
        $response = Curl::get('localhost:8888/header', [
            'headers' => [
                'Accept' => 'text/html',
            ],
        ]);

        $this->assertSame(
            'text/html',
            $response->getBody()->getContents()
        );
    }

    public function testHeadMethod(): void
    {
        $response = Curl::head('localhost:8888/method');

        $this->assertSame(
            '',
            $response->getBody()->getContents()
        );
    }

    public function testMacroable(): void
    {
        $this->assertContains(
            MacroTrait::class,
            class_uses(CurlResponse::class)
        );
    }

    public function testOptionsMethod(): void
    {
        $response = Curl::options('localhost:8888/method');

        $this->assertSame(
            'OPTIONS',
            $response->getBody()->getContents()
        );
    }

    public function testPatchData(): void
    {
        $response = Curl::patch('localhost:8888/json', [
            'value' => 1,
        ]);

        $this->assertSame(
            [
                'value' => 1,
            ],
            $response->getJson()
        );
    }

    public function testPatchMethod(): void
    {
        $response = Curl::patch('localhost:8888/method');

        $this->assertSame(
            'PATCH',
            $response->getBody()->getContents()
        );
    }

    public function testPostData(): void
    {
        $response = Curl::post('localhost:8888/post', [
            'value' => 1,
        ]);

        $this->assertSame(
            [
                'value' => '1',
            ],
            $response->getJson()
        );
    }

    public function testPostMethod(): void
    {
        $response = Curl::post('localhost:8888/method');

        $this->assertSame(
            'POST',
            $response->getBody()->getContents()
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
                'Content-Length' => (string) $length,
            ],
            'processData' => false,
        ]);

        $this->assertSame(
            [
                'value' => 1,
            ],
            $response->getJson()
        );
    }

    public function testProtocolVersion(): void
    {
        $response = Curl::get('localhost:8888/version', [
            'protocolVersion' => '1.0',
        ]);

        $this->assertSame(
            'HTTP/1.0',
            $response->getBody()->getContents()
        );
    }

    public function testPutData(): void
    {
        $response = Curl::put('localhost:8888/json', [
            'value' => 1,
        ]);

        $this->assertSame(
            [
                'value' => 1,
            ],
            $response->getJson()
        );
    }

    public function testPutMethod(): void
    {
        $response = Curl::put('localhost:8888/method');

        $this->assertSame(
            'PUT',
            $response->getBody()->getContents()
        );
    }

    public static function setUpBeforeClass(): void
    {
        self::$pid = (int) exec('nohup php -S localhost:8888 tests/Mock/server.php >/dev/null 2>&1 & echo $!');
        sleep(1);
    }

    public static function tearDownAfterClass(): void
    {
        exec('kill '.self::$pid.' 2>&1');
    }
}
