<?php
declare(strict_types=1);

namespace Fyre\CURL;

use Fyre\CURL\Exceptions\CurlException;
use Fyre\Http\Request;
use Fyre\Http\Stream;
use Fyre\Http\Uri;

use function array_key_exists;
use function array_keys;
use function array_map;
use function array_replace;
use function array_replace_recursive;
use function curl_close;
use function curl_error;
use function curl_exec;
use function curl_init;
use function curl_setopt_array;
use function explode;
use function fopen;
use function http_build_query;
use function is_file;
use function json_encode;
use function preg_match;
use function sleep;
use function strlen;
use function strpos;
use function strtolower;
use function strtoupper;
use function substr;
use function trim;

use const CURL_HTTP_VERSION_1_0;
use const CURL_HTTP_VERSION_1_1;
use const CURL_HTTP_VERSION_2_0;
use const CURLAUTH_BASIC;
use const CURLAUTH_DIGEST;
use const CURLOPT_CONNECTTIMEOUT_MS;
use const CURLOPT_COOKIEFILE;
use const CURLOPT_COOKIEJAR;
use const CURLOPT_CUSTOMREQUEST;
use const CURLOPT_ENCODING;
use const CURLOPT_FAILONERROR;
use const CURLOPT_FOLLOWLOCATION;
use const CURLOPT_FRESH_CONNECT;
use const CURLOPT_HEADER;
use const CURLOPT_HTTP_VERSION;
use const CURLOPT_HTTPAUTH;
use const CURLOPT_HTTPHEADER;
use const CURLOPT_MAXREDIRS;
use const CURLOPT_NOBODY;
use const CURLOPT_POSTFIELDS;
use const CURLOPT_POSTREDIR;
use const CURLOPT_REDIR_PROTOCOLS;
use const CURLOPT_RETURNTRANSFER;
use const CURLOPT_SSL_VERIFYPEER;
use const CURLOPT_SSLCERT;
use const CURLOPT_SSLCERTPASSWD;
use const CURLOPT_SSLKEY;
use const CURLOPT_STDERR;
use const CURLOPT_TIMEOUT_MS;
use const CURLOPT_URL;
use const CURLOPT_USERAGENT;
use const CURLOPT_USERPWD;
use const CURLOPT_VERBOSE;
use const CURLPROTO_HTTP;
use const CURLPROTO_HTTPS;

/**
 * CurlRequest
 */
class CurlRequest extends Request
{
    protected static array $defaults = [
        'method' => 'get',
        'headers' => [],
        'data' => null,
        'dataType' => null,
        'processData' => true,
        'userAgent' => null,
        'protocolVersion' => '1.1',
        'username' => null,
        'password' => null,
        'auth' => 'basic',
        'sslCert' => null,
        'sslPassword' => null,
        'sslKey' => null,
        'cookie' => null,
        'redirect' => true,
        'redirectOptions' => [
            'max' => 5,
            'strict' => true,
            'protocols' => CURLPROTO_HTTP | CURLPROTO_HTTPS,
        ],
        'verify' => true,
        'delay' => 0,
        'timeout' => 0,
        'connectTimeout' => 150,
        'debug' => false,
    ];

    protected string $auth;

    protected int $delay;

    protected array $options;

    /**
     * New CurlRequest constructor.
     *
     * @param Uri $uri The Uri.
     * @param array $options The request options.
     */
    public function __construct(Uri $uri, array $options = [])
    {
        $options = array_replace_recursive(static::$defaults, $options);

        [$uri, $options] = static::parseData($uri, $options);

        parent::__construct($uri, $options);

        $authOptions = static::parseAuth($options);
        $curlOptions = static::parseOptions($options);
        $redirectOptions = static::parseRedirect($options);
        $sslOptions = static::parseSsl($options);

        $this->options = array_replace($curlOptions, $authOptions, $redirectOptions, $sslOptions);
        $this->auth = $options['auth'];
        $this->delay = $options['delay'];
    }

    /**
     * Send the request.
     *
     * @return CurlResponse The CurlResponse.
     */
    public function send(): CurlResponse
    {
        $options = $this->options;

        $options[CURLOPT_URL] = (string) $this->uri;

        if (array_key_exists('Accept-Encoding', $this->headers)) {
            $options[CURLOPT_ENCODING] = $this->getHeaderLine('Accept-Encoding');
        }

        $options[CURLOPT_HTTPHEADER] = array_map(
            fn(string $name): string => $name.': '.$this->getHeaderLine($name),
            array_keys($this->headers)
        );

        $options[CURLOPT_CUSTOMREQUEST] = strtoupper($this->method);
        $options[CURLOPT_POSTFIELDS] = $this->body;

        if ($this->method === 'head') {
            $options[CURLOPT_NOBODY] = 1;
        }

        switch ($this->protocolVersion) {
            case '1.0':
                $options[CURLOPT_HTTP_VERSION] = CURL_HTTP_VERSION_1_0;
                break;
            case '1.1':
                $options[CURLOPT_HTTP_VERSION] = CURL_HTTP_VERSION_1_1;
                break;
            case '2.0':
                $options[CURLOPT_HTTP_VERSION] = CURL_HTTP_VERSION_2_0;
                break;
        }

        if ($this->delay > 0) {
            sleep($this->delay);
        }

        $content = $this->execute($options);

        $breakString = "\r\n\r\n";

        if (strpos($content, 'HTTP/1.1 100 Continue') === 0) {
            $content = substr($content, strpos($content, $breakString) + 4);
        }

        if (strpos($content, 'HTTP/1.1 200 Connection established') === 0) {
            $content = substr($content, strpos($content, $breakString) + 4);
        }

        if ($this->auth === 'digest' && strpos($content, 'WWW-Authenticate: Digest') !== false) {
            $content = substr($content, strpos($content, $breakString) + 4);
        }

        $break = strpos($content, $breakString);

        $response = new CurlResponse();

        if ($break !== false) {
            $headers = explode("\n", substr($content, 0, $break));

            foreach ($headers as $header) {
                if (strpos($header, 'HTTP') === 0) {
                    preg_match('/^HTTP\/([12](?:\.[01])?) (\d+) (.+)/', $header, $matches);

                    $response = $response
                        ->withStatus((int) $matches[2], $matches[3] ?? null)
                        ->withProtocolVersion($matches[1]);
                } else {
                    [$name, $value] = explode(':', $header, 2);
                    $value = trim($value);

                    $response = $response->withHeader($name, $value);
                }
            }

            $content = substr($content, $break + 4);
        }

        $stream = Stream::createFromString($content);

        $response = $response->withBody($stream);

        return $response;
    }

    /**
     * Execute the request.
     *
     * @param array $options The curl options.
     * @return string The response.
     *
     * @throws CurlException If the request encounters an error.
     */
    protected function execute(array $options): string
    {
        $ch = curl_init();

        curl_setopt_array($ch, $options);

        $output = curl_exec($ch);

        if ($output === false) {
            $message = curl_error($ch);
            throw new CurlException($message);
        }

        curl_close($ch);

        return $output;
    }

    /**
     * Parse the auth options.
     *
     * @param array $options The request options.
     * @return array The cURL auth options.
     */
    protected static function parseAuth(array $options): array
    {
        $result = [];

        if (!$options['username'] || !$options['password']) {
            return $result;
        }

        $result[CURLOPT_USERPWD] = $options['username'].':'.$options['password'];

        switch ($options['auth']) {
            case 'digest':
                $result[CURLOPT_HTTPAUTH] = CURLAUTH_DIGEST;
                break;
            default:
                $result[CURLOPT_HTTPAUTH] = CURLAUTH_BASIC;
                break;
        }

        return $result;
    }

    /**
     * Set the data options.
     *
     * @param Uri $uri The Uri.
     * @param array $options The request options.
     * @return array The Uri and the request options.
     */
    protected static function parseData(Uri $uri, array $options): array
    {
        if (!$options['data']) {
            return [$uri, $options];
        }

        if (strtolower($options['method']) === 'get') {
            $query = $uri->getQueryParams();
            $data = array_replace_recursive($query, $options['data']);
            $uri = $uri->withQueryParams($data);

            return [$uri, $options];
        }

        if (!$options['processData']) {
            $options['body'] = $options['data'];

            return [$uri, $options];
        }

        switch ($options['dataType']) {
            case 'json':
                $contentType = 'application/json';
                $data = json_encode($options['data']);

                $options['body'] = $data;
                break;
            default:
                $contentType = 'application/x-www-form-urlencoded';
                $data = http_build_query($options['data']);

                $options['body'] = $data;
                break;
        }

        $options['headers'] ??= [];
        $options['headers']['Content-Type'] = $contentType;
        $options['headers']['Content-Length'] = (string) strlen($data);

        return [$uri, $options];
    }

    /**
     * Parse the options.
     *
     * @param array $options The request options.
     * @return array The cURL options.
     */
    protected static function parseOptions(array $options): array
    {
        $result = [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => true,
            CURLOPT_FRESH_CONNECT => true,
            CURLOPT_TIMEOUT_MS => $options['timeout'] * 1000,
            CURLOPT_CONNECTTIMEOUT_MS => $options['connectTimeout'] * 1000,
            CURLOPT_FAILONERROR => $options['httpErrors'] ?? true,
        ];

        if ($options['userAgent']) {
            $result[CURLOPT_USERAGENT] = $options['userAgent'];
        }

        if ($options['cookie']) {
            $result[CURLOPT_COOKIEJAR] = $options['cookie'];
            $result[CURLOPT_COOKIEFILE] = $options['cookie'];
        }

        if ($options['debug']) {
            $result[CURLOPT_VERBOSE] = true;
            $result[CURLOPT_STDERR] = fopen('php://stderr', 'wb');
        }

        return $result;
    }

    /**
     * Parse the redirect options.
     *
     * @param array $options The request options.
     * @return array The cURL redirect options.
     */
    protected static function parseRedirect(array $options): array
    {
        $result = [];

        if (!$options['redirect']) {
            $result[CURLOPT_FOLLOWLOCATION] = false;

            return $result;
        }

        $result[CURLOPT_FOLLOWLOCATION] = true;
        $result[CURLOPT_MAXREDIRS] = $options['redirectOptions']['max'];

        if ($options['redirectOptions']['strict']) {
            $result[CURLOPT_POSTREDIR] = 1 | 2 | 4;
        }

        $result[CURLOPT_REDIR_PROTOCOLS] = $options['redirectOptions']['protocols'];

        return $result;
    }

    /**
     * Parse the SSL options.
     *
     * @param array $options The request options.
     * @return array The cURL SSL options.
     *
     * @throws CurlException if the SSL files are not valid.
     */
    protected static function parseSsl(array $options): array
    {
        $result = [];

        if ($options['sslCert']) {
            if (!is_file($options['sslCert'])) {
                throw CurlException::forInvalidSslFile($options['sslCert']);
            }

            $result[CURLOPT_SSLCERT] = $options['sslCert'];
        }

        if ($options['sslPassword']) {
            $result[CURLOPT_SSLCERTPASSWD] = $options['sslPassword'];
        }

        if ($options['sslKey']) {
            if (!is_file($options['sslKey'])) {
                throw CurlException::forInvalidSslFile($options['sslKey']);
            }

            $result[CURLOPT_SSLKEY] = $options['sslKey'];
        }

        $result[CURLOPT_SSL_VERIFYPEER] = $options['verify'];

        return $result;
    }
}
