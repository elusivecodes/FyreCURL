<?php
declare(strict_types=1);

namespace Fyre\CURL\Exceptions;

use RuntimeException;

/**
 * CurlException
 */
class CurlException extends RuntimeException
{
    public static function forInvalidSslFile(string $filePath): static
    {
        return new static('Invalid SSL file: '.$filePath);
    }
}
