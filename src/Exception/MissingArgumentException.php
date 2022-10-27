<?php

namespace Swiftly\Routing\Exception;

use LogicException;

use function sprintf;

/**
 * Exception to be thrown when a required route argument is missing
 */
Class MissingArgumentException Extends LogicException
{
    /**
     * Indicate the named `$argument` was not supplied
     *
     * @param string $argument Argument name
     */
    public function __construct(string $argument)
    {
        parent::__construct(
            sprintf(
                'Route requires argument "%s" but it was not supplied',
                $argument
            )
        );
    }
}
