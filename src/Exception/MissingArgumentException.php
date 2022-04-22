<?php

namespace Swiftly\Routing\Exception;

use LogicException;

use function sprintf;

/**
 * Exception thrown when a route is missing a required argument
 */
Class MissingArgumentException Extends LogicException
{

    /**
     * Indicates that the named route is missing the named argument
     *
     * @param string $name     Route name
     * @param string $argument Missing argument
     */
    public function __construct( string $name, string $argument )
    {
        parent::__construct(
            sprintf(
                'Route "%s" requires a value for parameter "%s" but none was given.',
                $name,
                $argument
            )
        );
    }
}
