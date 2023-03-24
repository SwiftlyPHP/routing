<?php

namespace Swiftly\Routing\Exception;

use Exception;

use function sprintf;

/**
 * Exception to be thrown when a URL does not appear to be valid
 */
class UrlParseException extends Exception
{
    public function __construct(string $url)
    {
        parent::__construct(
            sprintf(
                "Failed to parse '%s' it does not look like a valid URL",
                $url
            )
        );
    }
}
