<?php
declare(strict_types=1);

namespace Fyre\CURL;

use
    Fyre\CURL\Exceptions\CurlException,
    Fyre\Http\Header,
    Fyre\Http\Request,
    Fyre\Http\Response,
    Fyre\URI\Uri;

use const
    CURL_HTTP_VERSION_1_0,
    CURL_HTTP_VERSION_1_1,
    CURL_HTTP_VERSION_2_0,
    CURLAUTH_BASIC,
    CURLAUTH_DIGEST,
    CURLOPT_CONNECTTIMEOUT_MS,
    CURLOPT_COOKIEFILE,
    CURLOPT_COOKIEJAR,
    CURLOPT_CUSTOMREQUEST,
    CURLOPT_ENCODING,
    CURLOPT_FAILONERROR,
    CURLOPT_FOLLOWLOCATION,
    CURLOPT_FRESH_CONNECT,
    CURLOPT_HEADER,
    CURLOPT_HTTP_VERSION,
    CURLOPT_HTTPAUTH,
    CURLOPT_HTTPHEADER,
    CURLOPT_MAXREDIRS,
    CURLOPT_NOBODY,
    CURLOPT_POSTFIELDS,
    CURLOPT_POSTREDIR,
    CURLOPT_REDIR_PROTOCOLS,
    CURLOPT_RETURNTRANSFER,
    CURLOPT_SAFE_UPLOAD,
    CURLOPT_SSL_VERIFYPEER,
    CURLOPT_SSLCERT,
    CURLOPT_SSLCERTPASSWD,
    CURLOPT_SSLKEY,
    CURLOPT_STDERR,
    CURLOPT_TIMEOUT_MS,
    CURLOPT_URL,
    CURLOPT_USERAGENT,
    CURLOPT_USERPWD,
    CURLOPT_VERBOSE,
    CURLPROTO_HTTP,
    CURLPROTO_HTTPS;

use function
    array_map,
    array_replace_recursive,
    curl_close,
    curl_error,
    curl_exec,
    curl_init,
    curl_setopt_array,
    explode,
    fopen,
    http_build_query,
    is_array,
    is_file,
    json_encode,
    preg_match,
    sleep,
    strlen,
    strpos,
    strtoupper,
    substr;

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
            'protocols' => CURLPROTO_HTTP | CURLPROTO_HTTPS
        ],
        'verify' => true,
        'delay' => 0,
        'timeout' => 0,
        'connectTimeout' => 150,
        'debug' => false
    ];

    protected array $config;

    /**
     * New CurlRequest constructor.
     * @param Uri $uri The Uri.
     * @param array $options The request options.
     */
    public function __construct(Uri $uri, array $options = [])
    {
        parent::__construct($uri);

        $this->config = array_replace_recursive(static::$defaults, $options);

        $this->setMethod($this->config['method']);
        $this->setProtocolVersion($this->config['protocolVersion']);

        foreach ($this->config['headers'] AS $header => $value) {
            $this->setHeader($header, $value);
        }
    }

    /**
     * Send the request.
     * @return Response The Response.
     */
    public function send(): Response
    {
        $response = new Response();

        $options = [];

        $this->setAuthOptions($options);
        $this->setDataOptions($options);
        $this->setHeaderOptions($options);
        $this->setOptions($options);
        $this->setRedirectOptions($options);
        $this->setSslOptions($options);

        $options[CURLOPT_URL] = (string) $this->uri;
        $options[CURLOPT_CUSTOMREQUEST] = strtoupper($this->method);

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

        if ($this->config['delay'] > 0) {
            sleep($this->config['delay']);
        }

        $content = $this->execute($options);

        $breakString = "\r\n\r\n";

        if (strpos($content, 'HTTP/1.1 100 Continue') === 0) {
            $content = substr($content, strpos($content, $breakString) + 4);
        }

        if ($this->config['auth'] === 'digest' && strpos($content, 'WWW-Authenticate: Digest') !== false) {
            $content = substr($content, strpos($content, $breakString) + 4);
        }

        $break = strpos($content, $breakString);

        if ($break !== false) {
            $headers = explode("\n", substr($content, 0, $break));

            foreach ($headers as $header) {
                if (strpos($header, 'HTTP') === 0) {
                    preg_match('/^HTTP\/([12](?:\.[01])?) (\d+) (.+)/', $header, $matches);

                    $response->setProtocolVersion($matches[1]);
                    $response->setStatusCode((int) $matches[2], $matches[3] ?? null);
                } else {
                    [$name, $value] = explode(':', $header, 2);

                    $response->setHeader($name, $value);
                }
            }

            $content = substr($content, $break + 4);
        }

        $response->setBody($content);

        return $response;
    }

    /**
     * Execute the request.
     * @param array $options The curl options.
     * @return string The response.
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
     * Set the auth options.
     * @param array $options The options.
     */
    protected function setAuthOptions(array &$options): void
    {
        if (!$this->config['username'] || !$this->config['password']) {
            return;
        }

        $options[CURLOPT_USERPWD] = $this->config['username'].':'.$this->config['password'];

        switch ($this->config['auth']) {
            case 'digest':
                $options[CURLOPT_HTTPAUTH] = CURLAUTH_DIGEST;
                break;
            default:
                $options[CURLOPT_HTTPAUTH] = CURLAUTH_BASIC;
                break;
        }
    }

    /**
     * Set the data options.
     * @param array $options The options.
     */
    protected function setDataOptions(array &$options): void
    {
        if (!$this->config['data']) {
            return;
        }

        if ($this->method === 'get') {
            $query = $this->uri->getQuery();
            $data = array_replace_recursive($query, $this->config['data']);
            $this->uri->setQuery($data);
            return;
        }

        if (!$this->config['processData']) {
            $options[CURLOPT_POSTFIELDS] = $this->config['data'];
            return;
        }

        switch ($this->config['dataType']) {
            case 'json':
                $data = json_encode($this->config['data']);
                $length = strlen($data);

                $this->setHeader('Content-Type', 'application/json');
                $this->setHeader('Content-Length', (string) $length);
                $options[CURLOPT_POSTFIELDS] = $data;
                break;
            default:
                $data = http_build_query($this->config['data']);
                $length = strlen($data);

                $this->setHeader('Content-Type', 'application/x-www-form-urlencoded');
                $this->setHeader('Content-Length', (string) $length);

                $options[CURLOPT_POSTFIELDS] = $data;
                break;
        }
    }

    /**
     * Set the header options.
     * @param array $options The options.
     */
    protected function setHeaderOptions(array &$options): void
    {
        if ($this->config['userAgent']) {
            $options[CURLOPT_USERAGENT] = $this->config['userAgent'];
        }

        if ($this->headers === []) {
            return;
        }

        $accept = $this->getHeaderValue('Accept-Encoding');

        if ($accept) {
            $options[CURLOPT_ENCODING] = $accept;
        }

        $options[CURLOPT_HTTPHEADER] = array_map(
            fn(Header $header): string => (string) $header,
            $this->headers
        );
    }

    /**
     * Set the options.
     * @param array $options The options.
     */
    protected function setOptions(array &$options): void
    {
        $options[CURLOPT_RETURNTRANSFER] = true;
        $options[CURLOPT_HEADER] = true;
        $options[CURLOPT_FRESH_CONNECT] = true;
        $options[CURLOPT_SAFE_UPLOAD] = true;

        $options[CURLOPT_TIMEOUT_MS] = $this->config['timeout'] * 1000;
        $options[CURLOPT_CONNECTTIMEOUT_MS] = $this->config['connectTimeout'] * 1000;

        $options[CURLOPT_FAILONERROR] = $this->config['httpErrors'] ?? true;

        if ($this->method === 'head') {
            $options[CURLOPT_NOBODY] = 1;
        }

        if ($this->config['cookie']) {
            $options[CURLOPT_COOKIEJAR] = $this->config['cookie'];
            $options[CURLOPT_COOKIEFILE] = $this->config['cookie'];
        }

        if ($this->config['debug']) {
            $options[CURLOPT_VERBOSE] = true;
            $options[CURLOPT_STDERR] = fopen('php://stderr', 'wb');
        }
    }

    /**
     * Set the redirect options.
     * @param array $options The options.
     */
    protected function setRedirectOptions(array &$options): void
    {
        if (!$this->config['redirect']) {
            $options[CURLOPT_FOLLOWLOCATION] = false;
            return;
        }

        $options[CURLOPT_FOLLOWLOCATION] = true;
        $options[CURLOPT_MAXREDIRS] = $this->config['redirectOptions']['max'];

        if ($this->config['redirectOptions']['strict']) {
            $options[CURLOPT_POSTREDIR] = 1 | 2 | 4;
        }

        $options[CURLOPT_REDIR_PROTOCOLS] = $this->config['redirectOptions']['protocols'];
    }

    /**
     * Set the SSL options.
     * @param array $options The options.
     * @throws CurlException if the SSL files are invalid.
     */
    protected function setSslOptions(array &$options): void
    {
        if ($this->config['sslCert']) {
            if (!is_file($this->config['sslCert'])) {
                throw CurlException::forInvalidSslFile($this->config['sslCert']);
            }

            $options[CURLOPT_SSLCERT] = $this->config['sslCert'];
        }

        if ($this->config['sslPassword']) {
            $options[CURLOPT_SSLCERTPASSWD] = $this->config['sslPassword'];
        }

        if ($this->config['sslKey']) {
            if (!is_file($this->config['sslKey'])) {
                throw CurlException::forInvalidSslFile($this->config['sslKey']);
            }

            $options[CURLOPT_SSLKEY] = $this->config['sslKey'];
        }

        $options[CURLOPT_SSL_VERIFYPEER] = $this->config['verify'];
    }

}
