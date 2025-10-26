<?php
declare(strict_types=1);

namespace Fyre\CURL;

use Fyre\Http\Response;
use Fyre\Http\Uri;

/**
 * Curl
 */
abstract class Curl
{
    /**
     * Perform a DELETE request.
     *
     * @param string $url The url.
     * @param array $options The request options.
     * @return Response The Response.
     */
    public static function delete(string $url, array $options = []): Response
    {
        $options['method'] ??= 'delete';

        return static::request($url, $options);
    }

    /**
     * Perform a GET request.
     *
     * @param string $url The url.
     * @param array $options The request options.
     * @return Response The Response.
     */
    public static function get(string $url, array $options = []): Response
    {
        $options['method'] ??= 'get';

        return static::request($url, $options);
    }

    /**
     * Perform a HEAD request.
     *
     * @param string $url The url.
     * @param array $options The request options.
     * @return Response The Response.
     */
    public static function head(string $url, array $options = []): Response
    {
        $options['method'] ??= 'head';

        return static::request($url, $options);
    }

    /**
     * Perform an OPTIONS request.
     *
     * @param string $url The url.
     * @param array $options The request options.
     * @return Response The Response.
     */
    public static function options(string $url, array $options = []): Response
    {
        $options['method'] ??= 'options';

        return static::request($url, $options);
    }

    /**
     * Perform a PATCH request.
     *
     * @param string $url The url.
     * @param array $data The request data.
     * @param array $options The request options.
     * @return Response The Response.
     */
    public static function patch(string $url, array $data = [], array $options = []): Response
    {
        $options['method'] ??= 'patch';
        $options['data'] ??= $data;
        $options['dataType'] ??= 'json';

        return static::request($url, $options);
    }

    /**
     * Perform a POST request.
     *
     * @param string $url The url.
     * @param array $data The request data.
     * @param array $options The request options.
     * @return Response The Response.
     */
    public static function post(string $url, array $data = [], array $options = []): Response
    {
        $options['method'] ??= 'post';
        $options['data'] ??= $data;

        return static::request($url, $options);
    }

    /**
     * Perform a PUT request.
     *
     * @param string $url The url.
     * @param array $data The request data.
     * @param array $options The request options.
     * @return Response The Response.
     */
    public static function put(string $url, array $data = [], array $options = []): Response
    {
        $options['method'] ??= 'put';
        $options['data'] ??= $data;
        $options['dataType'] ??= 'json';

        return static::request($url, $options);
    }

    /**
     * Perform a request.
     *
     * @param string $url The url.
     * @param array $options The request options.
     * @return Response The Response.
     */
    protected static function request(string $url, array $options = []): Response
    {
        $uri = new Uri($url);
        $curl = new CurlRequest($uri, $options);

        return $curl->send();
    }
}
