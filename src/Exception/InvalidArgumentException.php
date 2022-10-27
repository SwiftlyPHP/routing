<?php

namespace Swiftly\Routing\Exception;

use InvalidArgumentException as NativeInvalidArgument;

use function sprintf;

/**
 * Exception to be thrown when a given route argument is the wrong type
 */
Class InvalidArgumentException Extends NativeInvalidArgument
{
    /**
     * Indicate the named `$argument` was of the wrong type
     *
     * @param string $argument Argument name
     * @param mixed $provided  Provided value
     */
    public function __construct(string $argument, $provided)
    {
        parent::__construct(
            sprintf(
                'Invalid value provided for route argument "%s", recieved "%s"',
                $argument,
                get_debug_type($provided)
            )
        );
    }
}
