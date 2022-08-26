<?php

namespace Swiftly\Routing\Exception;

use LogicException;

use function sprintf;

/**
 * Exception thrown when a route argument is provided but it's not a valid type
 */
Class InvalidArgumentException Extends LogicException
{

    /**
     * Indicates that the given argument is an invalid type
     *
     * @param string $route     Route name
     * @param string $parameter Parameter name
     */
    public function __construct( string $route, string $parameter )
    {
        parent::__construct(
            sprintf(
                'Invalid value given for parameter "%s" of route "%s"',
                $parameter,
                $route
            )
        );
    }
}
