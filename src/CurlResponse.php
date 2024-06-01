<?php
declare(strict_types=1);

namespace Fyre\CURL;

use Fyre\Http\Response;

use function json_decode;

class CurlResponse extends Response
{

    /**
     * Get the response body as decoded JSON.
     * @param bool $associative Whether to return JSON object as associative array.
     * @param int $depth The maximum depth of nesting.
     * @param int $flags The JSON decoding flags.
     * @return mixed The decoded JSON data.
     */
    public function getJson(bool $associative = true, int $depth = 512, int $flags = 0): mixed
    {
        return json_decode($this->body, $associative, $depth, $flags);
    }

}
