# FyreCURL

**FyreCURL** is a free, open-source cURL request library for *PHP*.


## Table Of Contents
- [Installation](#installation)
- [Methods](#methods)
- [Curl Requests](#curl-requests)
- [Curl Responses](#curl-responses)



## Installation

**Using Composer**

```
composer require fyre/curl
```

In PHP:

```php
use Fyre\CURL\Curl;
```


## Methods

**Delete**

Perform a DELETE request.

- `$url` is a string representing the URL.
- `$options` is an array containing the request options.
    - `headers` is an array containing additional headers to set, and will default to *[]*.
    - `userAgent` is a string representing the user agent, and will default to *null*.
    - `protocolVersion` is a string representing the HTTP protocol version, and will default to "*1.1*".
    - `username` is a string representing the HTTP authentication username, and will default to *null*.
    - `password` is a string representing the HTTP authentication password, and will default to *null*.
    - `auth` is a string representing the authentication method, and will default to "*basic*".
    - `sslCert` is a string representing the file path of the SSL certificate, and will default to *null*.
    - `sslPassword` is a string representing the SSL certificate password, and will default to *null*.
    - `sslKey` is a string representing the file path of the SSL key, and will default to *null*.
    - `cookie` is a string representing the file path of the cookie file, and will default to *null*.
    - `redirect` is a boolean indicating whether to allow redirects, and will default to *true*.
    - `redirectOptions` is an array containing redirect options.
        - `max` is a number representing the maximum number of redirects, and will default to *5*.
        - `strict` is a boolean indicating whether to only follow 301, 302 and 303 redirects, and will default to *true*.
        - `protocols` is an integer bitmask representing the protocols to allow redirects for, and will default to *CURLPROTO_HTTP | CURLPROTO_HTTPS*.
    - `verify` is a boolean indicating whether to verify the peer SSL certificate, and will default to *true*.
    - `delay` is a number representing the number of seconds to wait before executing the request, and will default to *0*.
    - `timeout` is a number representing the maximum number of seconds for cURL to initialize, and will default to *0*.
    - `connectTimeout` is a number representing the maximum number of seconds trying to connect, and will default to *150*.

```php
$response = Curl::delete($url, $options);
```

This method returns a new [*CurlResponse*](#curl-responses).

**Get**

Perform a GET request.

- `$url` is a string representing the URL.
- `$options` is an array containing the request options.
    - `headers` is an array containing additional headers to set, and will default to *[]*.
    - `data` is an array containing additional data to send with the request, and will default to *null*.
    - `userAgent` is a string representing the user agent, and will default to *null*.
    - `protocolVersion` is a string representing the HTTP protocol version, and will default to "*1.1*".
    - `username` is a string representing the HTTP authentication username, and will default to *null*.
    - `password` is a string representing the HTTP authentication password, and will default to *null*.
    - `auth` is a string representing the authentication method, and will default to "*basic*".
    - `sslCert` is a string representing the file path of the SSL certificate, and will default to *null*.
    - `sslPassword` is a string representing the SSL certificate password, and will default to *null*.
    - `sslKey` is a string representing the file path of the SSL key, and will default to *null*.
    - `cookie` is a string representing the file path of the cookie file, and will default to *null*.
    - `redirect` is a boolean indicating whether to allow redirects, and will default to *true*.
    - `redirectOptions` is an array containing redirect options.
        - `max` is a number representing the maximum number of redirects, and will default to *5*.
        - `strict` is a boolean indicating whether to only follow 301, 302 and 303 redirects, and will default to *true*.
        - `protocols` is an integer bitmask representing the protocols to allow redirects for, and will default to *CURLPROTO_HTTP | CURLPROTO_HTTPS*.
    - `verify` is a boolean indicating whether to verify the peer SSL certificate, and will default to *true*.
    - `delay` is a number representing the number of seconds to wait before executing the request, and will default to *0*.
    - `timeout` is a number representing the maximum number of seconds for cURL to initialize, and will default to *0*.
    - `connectTimeout` is a number representing the maximum number of seconds trying to connect, and will default to *150*.

```php
$response = Curl::get($url, $options);
```

This method returns a new [*CurlResponse*](#curl-responses).

**Head**

Perform a HEAD request.

- `$url` is a string representing the URL.
- `$options` is an array containing the request options.
    - `headers` is an array containing additional headers to set, and will default to *[]*.
    - `userAgent` is a string representing the user agent, and will default to *null*.
    - `protocolVersion` is a string representing the HTTP protocol version, and will default to "*1.1*".
    - `username` is a string representing the HTTP authentication username, and will default to *null*.
    - `password` is a string representing the HTTP authentication password, and will default to *null*.
    - `auth` is a string representing the authentication method, and will default to "*basic*".
    - `sslCert` is a string representing the file path of the SSL certificate, and will default to *null*.
    - `sslPassword` is a string representing the SSL certificate password, and will default to *null*.
    - `sslKey` is a string representing the file path of the SSL key, and will default to *null*.
    - `cookie` is a string representing the file path of the cookie file, and will default to *null*.
    - `redirect` is a boolean indicating whether to allow redirects, and will default to *true*.
    - `redirectOptions` is an array containing redirect options.
        - `max` is a number representing the maximum number of redirects, and will default to *5*.
        - `strict` is a boolean indicating whether to only follow 301, 302 and 303 redirects, and will default to *true*.
        - `protocols` is an integer bitmask representing the protocols to allow redirects for, and will default to *CURLPROTO_HTTP | CURLPROTO_HTTPS*.
    - `verify` is a boolean indicating whether to verify the peer SSL certificate, and will default to *true*.
    - `delay` is a number representing the number of seconds to wait before executing the request, and will default to *0*.
    - `timeout` is a number representing the maximum number of seconds for cURL to initialize, and will default to *0*.
    - `connectTimeout` is a number representing the maximum number of seconds trying to connect, and will default to *150*.

```php
$response = Curl::head($url, $options);
```

This method returns a new [*CurlResponse*](#curl-responses).

**Options**

Perform an OPTIONS request.

- `$url` is a string representing the URL.
- `$options` is an array containing the request options.
    - `headers` is an array containing additional headers to set, and will default to *[]*.
    - `userAgent` is a string representing the user agent, and will default to *null*.
    - `protocolVersion` is a string representing the HTTP protocol version, and will default to "*1.1*".
    - `username` is a string representing the HTTP authentication username, and will default to *null*.
    - `password` is a string representing the HTTP authentication password, and will default to *null*.
    - `auth` is a string representing the authentication method, and will default to "*basic*".
    - `sslCert` is a string representing the file path of the SSL certificate, and will default to *null*.
    - `sslPassword` is a string representing the SSL certificate password, and will default to *null*.
    - `sslKey` is a string representing the file path of the SSL key, and will default to *null*.
    - `cookie` is a string representing the file path of the cookie file, and will default to *null*.
    - `redirect` is a boolean indicating whether to allow redirects, and will default to *true*.
    - `redirectOptions` is an array containing redirect options.
        - `max` is a number representing the maximum number of redirects, and will default to *5*.
        - `strict` is a boolean indicating whether to only follow 301, 302 and 303 redirects, and will default to *true*.
        - `protocols` is an integer bitmask representing the protocols to allow redirects for, and will default to *CURLPROTO_HTTP | CURLPROTO_HTTPS*.
    - `verify` is a boolean indicating whether to verify the peer SSL certificate, and will default to *true*.
    - `delay` is a number representing the number of seconds to wait before executing the request, and will default to *0*.
    - `timeout` is a number representing the maximum number of seconds for cURL to initialize, and will default to *0*.
    - `connectTimeout` is a number representing the maximum number of seconds trying to connect, and will default to *150*.

```php
$response = Curl::options($url, $options);
```

This method returns a new [*CurlResponse*](#curl-responses).

**Patch**

Perform a PATCH request.

- `$url` is a string representing the URL.
- `$data` is an array containing the data.
- `$options` is an array containing the request options.
    - `headers` is an array containing additional headers to set, and will default to *[]*.
    - `dataType` is a string representing the type of data to send with the request, and will default to "*json*".
    - `userAgent` is a string representing the user agent, and will default to *null*.
    - `protocolVersion` is a string representing the HTTP protocol version, and will default to "*1.1*".
    - `username` is a string representing the HTTP authentication username, and will default to *null*.
    - `password` is a string representing the HTTP authentication password, and will default to *null*.
    - `auth` is a string representing the authentication method, and will default to "*basic*".
    - `sslCert` is a string representing the file path of the SSL certificate, and will default to *null*.
    - `sslPassword` is a string representing the SSL certificate password, and will default to *null*.
    - `sslKey` is a string representing the file path of the SSL key, and will default to *null*.
    - `cookie` is a string representing the file path of the cookie file, and will default to *null*.
    - `redirect` is a boolean indicating whether to allow redirects, and will default to *true*.
    - `redirectOptions` is an array containing redirect options.
        - `max` is a number representing the maximum number of redirects, and will default to *5*.
        - `strict` is a boolean indicating whether to only follow 301, 302 and 303 redirects, and will default to *true*.
        - `protocols` is an integer bitmask representing the protocols to allow redirects for, and will default to *CURLPROTO_HTTP | CURLPROTO_HTTPS*.
    - `verify` is a boolean indicating whether to verify the peer SSL certificate, and will default to *true*.
    - `delay` is a number representing the number of seconds to wait before executing the request, and will default to *0*.
    - `timeout` is a number representing the maximum number of seconds for cURL to initialize, and will default to *0*.
    - `connectTimeout` is a number representing the maximum number of seconds trying to connect, and will default to *150*.

```php
$response = Curl::patch($url, $data, $options);
```

This method returns a new [*CurlResponse*](#curl-responses).

**Post**

Perform a POST request.

- `$url` is a string representing the URL.
- `$data` is an array containing the data.
- `$options` is an array containing the request options.
    - `headers` is an array containing additional headers to set, and will default to *[]*.
    - `dataType` is a string representing the type of data to send with the request, and will default to *null*.
    - `userAgent` is a string representing the user agent, and will default to *null*.
    - `protocolVersion` is a string representing the HTTP protocol version, and will default to "*1.1*".
    - `username` is a string representing the HTTP authentication username, and will default to *null*.
    - `password` is a string representing the HTTP authentication password, and will default to *null*.
    - `auth` is a string representing the authentication method, and will default to "*basic*".
    - `sslCert` is a string representing the file path of the SSL certificate, and will default to *null*.
    - `sslPassword` is a string representing the SSL certificate password, and will default to *null*.
    - `sslKey` is a string representing the file path of the SSL key, and will default to *null*.
    - `cookie` is a string representing the file path of the cookie file, and will default to *null*.
    - `redirect` is a boolean indicating whether to allow redirects, and will default to *true*.
    - `redirectOptions` is an array containing redirect options.
        - `max` is a number representing the maximum number of redirects, and will default to *5*.
        - `strict` is a boolean indicating whether to only follow 301, 302 and 303 redirects, and will default to *true*.
        - `protocols` is an integer bitmask representing the protocols to allow redirects for, and will default to *CURLPROTO_HTTP | CURLPROTO_HTTPS*.
    - `verify` is a boolean indicating whether to verify the peer SSL certificate, and will default to *true*.
    - `delay` is a number representing the number of seconds to wait before executing the request, and will default to *0*.
    - `timeout` is a number representing the maximum number of seconds for cURL to initialize, and will default to *0*.
    - `connectTimeout` is a number representing the maximum number of seconds trying to connect, and will default to *150*.

```php
$response = Curl::post($url, $data, $options);
```

This method returns a new [*CurlResponse*](#curl-responses).

**Put**

Perform a PUT request.

- `$url` is a string representing the URL.
- `$data` is an array containing the data.
- `$options` is an array containing the request options.
    - `headers` is an array containing additional headers to set, and will default to *[]*.
    - `dataType` is a string representing the type of data to send with the request, and will default to "*json*".
    - `userAgent` is a string representing the user agent, and will default to *null*.
    - `protocolVersion` is a string representing the HTTP protocol version, and will default to "*1.1*".
    - `username` is a string representing the HTTP authentication username, and will default to *null*.
    - `password` is a string representing the HTTP authentication password, and will default to *null*.
    - `auth` is a string representing the authentication method, and will default to "*basic*".
    - `sslCert` is a string representing the file path of the SSL certificate, and will default to *null*.
    - `sslPassword` is a string representing the SSL certificate password, and will default to *null*.
    - `sslKey` is a string representing the file path of the SSL key, and will default to *null*.
    - `cookie` is a string representing the file path of the cookie file, and will default to *null*.
    - `redirect` is a boolean indicating whether to allow redirects, and will default to *true*.
    - `redirectOptions` is an array containing redirect options.
        - `max` is a number representing the maximum number of redirects, and will default to *5*.
        - `strict` is a boolean indicating whether to only follow 301, 302 and 303 redirects, and will default to *true*.
        - `protocols` is an integer bitmask representing the protocols to allow redirects for, and will default to *CURLPROTO_HTTP | CURLPROTO_HTTPS*.
    - `verify` is a boolean indicating whether to verify the peer SSL certificate, and will default to *true*.
    - `delay` is a number representing the number of seconds to wait before executing the request, and will default to *0*.
    - `timeout` is a number representing the maximum number of seconds for cURL to initialize, and will default to *0*.
    - `connectTimeout` is a number representing the maximum number of seconds trying to connect, and will default to *150*.

```php
$response = Curl::put($url, $data, $options);
```

This method returns a new [*CurlResponse*](#curl-responses).


## Curl Requests

This class extends the [*Request*](https://github.com/elusivecodes/FyreRequest) class.

```php
use Fyre\CURL\CurlRequest;
```

- `$uri` is a [*Uri*](https://github.com/elusivecodes/FyreURI).
- `$options` is an array containing the request options.
    - `headers` is an array containing additional headers to set, and will default to *[]*.
    - `data` is an array containing additional data to send with the request, and will default to *null*.
    - `dataType` is a string representing the type of data to send with the request, and will default to "*json*".
    - `userAgent` is a string representing the user agent, and will default to *null*.
    - `protocolVersion` is a string representing the HTTP protocol version, and will default to "*1.1*".
    - `username` is a string representing the HTTP authentication username, and will default to *null*.
    - `password` is a string representing the HTTP authentication password, and will default to *null*.
    - `auth` is a string representing the authentication method, and will default to "*basic*".
    - `sslCert` is a string representing the file path of the SSL certificate, and will default to *null*.
    - `sslPassword` is a string representing the SSL certificate password, and will default to *null*.
    - `sslKey` is a string representing the file path of the SSL key, and will default to *null*.
    - `cookie` is a string representing the file path of the cookie file, and will default to *null*.
    - `redirect` is a boolean indicating whether to allow redirects, and will default to *true*.
    - `redirectOptions` is an array containing redirect options.
        - `max` is a number representing the maximum number of redirects, and will default to *5*.
        - `strict` is a boolean indicating whether to only follow 301, 302 and 303 redirects, and will default to *true*.
        - `protocols` is an integer bitmask representing the protocols to allow redirects for, and will default to *CURLPROTO_HTTP | CURLPROTO_HTTPS*.
    - `verify` is a boolean indicating whether to verify the peer SSL certificate, and will default to *true*.
    - `delay` is a number representing the number of seconds to wait before executing the request, and will default to *0*.
    - `timeout` is a number representing the maximum number of seconds for cURL to initialize, and will default to *0*.
    - `connectTimeout` is a number representing the maximum number of seconds trying to connect, and will default to *150*.

```php
$request = new CurlRequest($url, $options);
```

**Send**

Send the request.

```php
$response = $request->send();
```

This method returns a new [*CurlResponse*](#curl-responses).


## Curl Responses

This class extends the [*Response*](https://github.com/elusivecodes/FyreResponse) class.

```php
use Fyre\CURL\CurlResponse;
```

**Get Json**

Get the response body as decoded JSON.

- `$associative` is a boolean indicating whether to return JSON object as associative array, and will default to *true*.
- `$depth` is a number representing the maximum depth of nesting, and will default to *512*.
- `$flags` is a number representing additional flags to use for decoding, and will default to *0*.

```php
$data = $response->getJson($associative, $depth, $flags);
```