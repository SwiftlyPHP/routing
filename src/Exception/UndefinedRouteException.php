<?php declare(strict_types=1);

namespace Swiftly\Routing\Exception;

use LogicException;

use function sprintf;

/**
 * Exception to be thrown when trying to reference a non-existent route
 */
class UndefinedRouteException extends LogicException
{
    /**
     * Indicates the named `$route` could not be found or does not exists
     *
     * @param string $route Route name
     */
    public function __construct(string $route)
    {
        parent::__construct(
            sprintf(
                "Failed to find a route named '%s', are you sure it exists?",
                $route
            )
        );
    }
}
