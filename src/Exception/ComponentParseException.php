<?php

namespace Swiftly\Routing\Exception;

use Exception;

use function sprintf;

/**
 * Exception to be thrown when parsing of path components fails
 */
class ComponentParseException extends Exception
{
    public function __construct(string $url)
    {
        parent::__construct(
            sprintf(
                "Failed to parse components in (%s), possible syntax error",
                $url
            )
        );
    }
}
