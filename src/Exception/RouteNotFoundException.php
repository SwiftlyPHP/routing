<?php

namespace Swiftly\Routing\Exception;

use LogicException;

use function sprintf;

/**
 * Exception thrown when a named route cannot be found
 */
Class RouteNotFoundException Extends LogicException
{

    /**
     * Indicates that the named route was not found
     *
     * @param string $name Route name
     */
    public function __construct( string $name )
    {
        parent::__construct(
            sprintf(
                'Could not find a route with the name "%s", are you sure it exists?',
                $name
            )
        );
    }
}
