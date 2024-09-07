<?php declare(strict_types=1);

namespace Swiftly\Routing\Exception;

use Exception;

/**
 * Exception to be thrown if we fail while trying to parse a route definition
 *
 * @todo Create specific 'missing attribute' sub-classes
 */
class RouteParseException extends Exception
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}
